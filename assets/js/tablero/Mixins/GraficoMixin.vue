<script>
    export default {
        data : function() {
            return {
                doubleClickTime : 0,
                doubleClickThreshold : 500,
                seleccionActiva : false,
                dec : ( isNaN( this.indicador.ficha.cantidad_decimales) || this.indicador.ficha.cantidad_decimales == null) ? 2 : this.indicador.ficha.cantidad_decimales
            }
        },

        computed : {
            nombreDimension : function (){
                return ( this.indicador.dimension != undefined && this.indicador.informacion.dimensiones!= undefined) ?
                    this.indicador.informacion.dimensiones[this.indicador.dimension].descripcion.toUpperCase() : ''
            },

            datosOrdenados : function() {
                let datos_ = this.indicador.data.map(f => {
                    return { x: f.category, y: f.measure };
                });

                return this.aplicarOrden( datos_);
            }
        },
        methods: {
            // method
            getColores : function(datos_, rangos) {
                var indice = 0;
                var colores_ = this.$store.state.colores_;

                return datos_.map(f => {
                    var color = this.getColor(f.measure, rangos);

                    if (color == "white") {
                        color = colores_[indice];
                        indice = indice == colores_.length - 1 ? 0 : indice + 1;
                    }

                    return { x: f.category, color: color };
                });
            },
            getColor : function ( valor, rangos ){
                let color = '';
                rangos.forEach(rango => {
                    if (valor >= rango.limite_inf && valor <= rango.limite_sup) {
                        color = rango.color;
                    }
                });
                return (color == "") ? "white": color;
            },

            singleClick : function(eventData) {

                //Si es un gráfico de comparación por dimensión, no permitir agregar filtro al dar clic al gráfico
                if ( this.indicador.configuracion.dimensionComparacion == '' ){
                    if ( ['PIECHART', 'PIE', 'PASTEL', 'TORTA'].includes(this.indicador.configuracion.tipo_grafico.toUpperCase() ) ){
                        this.$emit("click-plot", eventData.points[0].label);
                    } else {
                        this.$emit("click-plot", eventData.points[0].x);
                    }
                }
            },

            click : function (eventData) {
                // Click fires once on single and twice on double clicks
                // We only care about single clicks.
                //This checks to give the doubleclick event 500 ms to fire, and does nothing if so
                var t0 = Date.now();
                var _this = this;
                if (t0 - this.doubleClickTime > this.doubleClickThreshold) {
                    setTimeout(function() {
                        if (t0 - _this.doubleClickTime > _this.doubleClickThreshold) {
                            _this.singleClick(eventData);
                        }
                    }, this.doubleClickThreshold);
                }
            },

            doubleclick : function() {
                console.log("doble clic");
                this.doubleClickTime = Date.now();
                this.$emit("doubleclick");
            },

            selected : function( eventData ) {
                if (eventData != undefined) {
                    this.$emit("filtar-posicion", eventData.points);
                }
            },

            // Triggered when you double-click to turn off the lasso or box selection
            deselect : function( eventData ) {
                this.doubleClickTime = Date.now();
                this.$emit("quitar-filtros", eventData);
            },

            aplicarOrden : function ( data) {

                let datos_ = data;
                //Aplicar otros filtros
                if (this.indicador.otros_filtros.elementos.length > 0) {
                    let filtros_ = this.indicador.otros_filtros.elementos;
                    datos_ = datos_.filter(d => {
                        return filtros_.includes(d.x);
                    });
                }

                //Verificar primero si tiene orden X

                if ( this.indicador.configuracion.orden_x != "") {
                    datos_ =
                        this.indicador.configuracion.orden_x == "asc"
                            ? datos_.sort((a, b) => (isNaN(a.x) || isNaN(b.x) ) ?  a.x.localeCompare(b.x) : a.x - b.x )
                            : datos_.sort((a, b) => (isNaN(a.x) || isNaN(b.x) ) ?  b.x.localeCompare(a.x) : b.x - a.x );
                } else if ( this.indicador.configuracion.orden_y != "") {
                    datos_ =
                        this.indicador.configuracion.orden_y == "asc"
                            ? datos_.sort((a, b) => a.y - b.y)
                            : datos_.sort((a, b) => b.y - a.y);
                }

                return datos_;
            }
        }
    }
</script>