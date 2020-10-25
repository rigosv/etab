<template>
    <div>
        <Plotly :id="'grafico-'+index" ref="grafico" :data="datos" :layout="layout"  :display-mode-bar="false" 
            v-if="indicador.data.length > 0"
            @click="click"
            @doubleclick= "doubleclick"
            @selected= "selected"
            @deselect= "deselect"
            >
        </Plotly>
    </div>
</template>

<script>
    import {Plotly} from 'vue-plotly';
    import numeral from 'numeral';

    import GraficoMixin from '../Mixins/GraficoMixin';
    
    export default {
        components : { Plotly },
        mixins: [GraficoMixin],
        props: {
            indicador: Object,
            index: Number
        },

        computed : {
            //computed
            tipoGrafico : function() {
                if ( ['BARRA', 'BARRAS', 'COLUMNAS', 'COLUMNA', 'DISCRETEBARCHART'].includes(this.indicador.configuracion.tipo_grafico.toUpperCase() ) ){
                    return 'bar';
                }else if ( ['LINECHART', 'LINEA', 'LINEAS'].includes(this.indicador.configuracion.tipo_grafico.toUpperCase()) ){
                    return 'scatter';
                } else if (['PIECHART', 'PIE', 'PASTEL', 'TORTA'].includes(this.indicador.configuracion.tipo_grafico.toUpperCase()) ) {
                    return 'pie';
                } else if (['BOX'].includes(this.indicador.configuracion.tipo_grafico.toUpperCase())){
                    return 'box';
                } else if (['BURBUJA'].includes(this.indicador.configuracion.tipo_grafico.toUpperCase())){
                    return 'burbuja';
                }
            },
            datos : function () {

                let traces = [];
                if ( this.indicador.configuracion.dimensionComparacion == '' ) {
                    let trace0 = this.getDataTrace(this.datosOrdenados, 1);
                    traces.push(trace0);
                    if (this.indicador.dataComparar.length == 0 && this.indicador.informacion.meta != null) {
                        traces.push(this.agregarMeta());
                    }

                    if (this.tipoGrafico != 'burbuja' && this.indicador.dataComparar.length > 0) {
                        this.indicador.dataComparar.forEach((ind, index_) => {
                            let data_ = ind.data.map(f => {
                                return {x: f.category, y: f.measure}
                            });

                            //Incluir solo los elementos que también existan en el indicador principal     
                            let filtros_ = this.indicador.otros_filtros.elementos;
                            if (filtros_.length > 0) {
                                data_ = data_.filter(d => {
                                    return filtros_.includes(d.x)
                                });
                            }

                            traces.push(this.getDataTrace(this.aplicarOrden(data_), parseInt(index_) + 2));
                        });
                    }
                } else {
                    traces = this.datosComparacionDimension();
                    if ( this.indicador.informacion.meta != null) {
                        traces.push(this.agregarMeta());
                    }
                }

                return traces;
            },
            
            layout : function() {
                let titulo = ( this.indicador.nombre.match(/.{1,40}/g) != null && this.indicador.dataComparar.length == 0 ) ? this.indicador.nombre.match(/.{1,40}/g).join('<BR>') : '';
                let height_ = ( this.indicador.full_screen ) ?  window.innerHeight / 1.15 : 
                                parseFloat(this.indicador.configuracion.layout.h) * 30 - 100;

                let layout_ =  {
                    title: titulo,
                    height :  height_ ,
                    autosize : true,
                    yaxis: { title: this.indicador.informacion.unidad_medida, exponentformat: 'none' },
                    xaxis: { title: this.nombreDimension, exponentformat: 'none', type: 'category'}
                    
                };

                if ( this.tipoGrafico == 'pie'){
                    layout_.margin = {l:30, r:30, b:30, t:30, pad:10};
                } else {
                    layout_.dragmode = 'select';
                    layout_.selectdirection = 'h'; 
                    layout_.margin = {l:50, r:5, b:50, t:30, pad:4};               
                }
                if ( this.indicador.configuracion.dimensionComparacion != '' ){
                    layout_.yaxis.fixedrange = layout_.xaxis.fixedrange = true;
                }


                return layout_;
            },

        },
        methods : {

            agregarMeta :  function ( ) {
                const meta = this.indicador.informacion.meta;
                if ( meta != null ) {
                    const category = [...new Set (this.datosOrdenados.map( d => d.x))];
                    const max = category[category.length - 1];
                    const min = category[0];
                    return {
                        x: [min, max],
                        y: [meta, meta],
                        type: 'scatter',
                        mode: 'lines',
                        textposition: 'bottom',
                        showlegend: false,
                        hoverinfo: 'none',
                        name : this.$t('_meta_'),
                        hovertemplate : '%{y:,}',
                        line: {
                            color: 'black',
                            dash: 'dash'
                        }
                    };

                }
            },
            getDataTrace : function ( data_, pos ){
                let x = data_.map( f => { return  f.x } );
                let y = data_.map( f => { return  f.y  } );
                //Asignar un color a cada elemento X y mantenerlo aunque se hayan filtrado, para que al redibujar el gráfico filtrado no le cambie de color al elemento del gráfico
                let rangos = (pos == 1) ? this.indicador.informacion.rangos : this.indicador.dataComparar[pos-2].informacion.rangos;
                let colores = this.getColores( this.indicador.data, rangos).filter( c => { return x.includes(c.x) }).map( c => { return c.color });
                let trace = {};
                //El tipo burbuja solo se mostrará cuando se estén comparando indicadores
                // El eje y será el valor de un indicador y el diámetro de la burbuja será el valor del otro indicador
                // solo se utilizará al comparar dos indicadores, si hay más no se tomarán en cuenta
                if ( this.tipoGrafico == 'burbuja' && this.indicador.dataComparar.length > 0 ){
                    
                    //Buscar el tamaño de la burbuja en el otro indicador a comparar
                    let size_ = x.map( x_ => { return this.indicador.dataComparar[0].data.find( d => { return d.category == x_ }) });
                    //Asignar 1 a los valores que no tengan correspondencia en el paso anterior, y escalarlos
                    size_ = size_.map ( v => { return   ( v == undefined) ? 1 : parseFloat(v.measure)  });
                    //Escalar los datos, 2000 será el mayor tamaño de burbuja
                    let factor = 2000 / Math.max.apply(null, size_);
                    let size = size_.map ( v => { return   v * factor  });

                    let nombre = this.indicador.dataComparar[0].nombre;
                    let nombreIndC = ( this.indicador.full_screen ) ? nombre
                                    : nombre.substring(0,30) + ( (nombre.length > 0) ? '...' : '');

                    let dec = (isNaN( this.indicador.ficha.cantidad_decimales) ) ? 2 : this.indicador.ficha.cantidad_decimales;
                    let texto = size_.map( v => { return '(' + numeral( v ).format('0,0.'+'0'.repeat(this.dec)) + ') ' + nombreIndC });
                
                    return { x: x, y: y,
                        text: texto,
                        mode: 'markers',
                        marker: {
                            color: this.$store.state.colores_,
                            size: size,
                            sizemode: 'area'
                        }
                    };
                } else if ( this.tipoGrafico == 'pie' ){
                    return { labels: x, values: y,
                        type: 'pie',
                        textinfo: 'label+value',
                        hoverinfo: 'label+value',
                        showlegend: false,
                    };
                } else if (this.tipoGrafico == 'box' ){
                    return { y: y,
                        type: 'box',
                        boxmean: 'sd',
                        boxpoints: 'all',
                        jitter: 0.3,
                        whiskerwidth: 0.2,
                        fillcolor: 'cls',
                        marker: {
                            size: 4
                        },
                        line: {
                            width: 1
                        },
                        showlegend: this.indicador.dataComparar.length > 0 ,
                        name: pos + ' - ' +  ( (pos == 1) ? this.indicador.nombre : this.indicador.dataComparar[pos-2].nombre )
                    };                                               
                } else {
                    return  { x: x, y: y,
                            type: this.tipoGrafico,
                            text: y.map( v => numeral( v ).format('0,0.'+'0'.repeat(this.dec)) ),
                            textposition: 'auto',
                            hoverinfo: 'text',
                            hovertemplate :'%{x}<br><b>%{y:,}</b>',
                            marker:{ opacity: 0.6, color : colores, size: 14 },
                            showlegend: this.indicador.dataComparar.length > 0 ,
                            name: pos + '.- ' +  ( (pos == 1) ? this.indicador.nombre : this.indicador.dataComparar[pos-2].nombre )
                        };
                    
                }

                
            },

            downloadImage : function (options) {                
                this.$refs.grafico.downloadImage(options);
            },
            react : function () {
                this.$refs.grafico.schedule({ replot: true });
            },

            toImage : function ( options ) {
                return this.$refs.grafico.toImage(options);
            },

            datosComparacionDimension : function () {
                let traces = [];

                const subcategorias = [...new Set (this.indicador.data.map( d => d.subcategory))];

                const rangos = this.indicador.informacion.rangos;            

                subcategorias.map( (sc, index) => {
                    let serie = this.indicador.data.filter( d => d.subcategory == sc);
                    serie  = this.indicador.configuracion.orden_x == "desc"
                        ? serie.sort((a, b) => (isNaN(a.category) || isNaN(b.category) ) ?  b.category.localeCompare(a.category) : b.category - a.category )
                        : serie.sort((a, b) => (isNaN(a.category ) || isNaN(b.category) ) ?  a.category.localeCompare(b.category) : a.category - b.category );

                    const x = serie.map( s => s.category );
                    const y = serie.map( s => s.measure );

                    let dataSerie = { x: x, y: y,
                        type: this.tipoGrafico,
                        text: y.map( v => numeral( v ).format('0,0.'+'0'.repeat(this.dec)) ),
                        textposition: 'auto',
                        hoverinfo: 'text',
                        hovertemplate :'%{x}<br><b>%{y:,}</b>',
                        marker:{ opacity: 0.6, size: 14 },
                        showlegend: true,
                        name: '<B>' + (parseInt(index) + 1) + '.- </B> ' + sc
                    };

                    if ( rangos.length > 0 ){
                        dataSerie.marker.color = y.map( v => this.getColor(v, rangos) );

                    }

                    traces.push( dataSerie );
                });

                return traces;
            }
        }
        
    }
</script>