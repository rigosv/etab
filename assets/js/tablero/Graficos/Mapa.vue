<template>
    <div style="width: 100%; overflow:  hidden" :id="'grafico-'+index"
         :style=" {height : (( indicador.full_screen ) ?  (window_.innerHeight / 1.18)+'px': 
         (parseFloat(indicador.configuracion.layout.h) * 30 - 100) + 'px' )}"

    >
        <l-map :ref="'myMap'+index"
            style="width: 2024px; height : 1024px"

            :zoom="zoom"
            :center="center"
            @update:zoom="zoomUpdated"
            @update:center="centerUpdated"
            v-if="mapaDatosCargados"
        >
            <l-control position="topleft" >
                <DIV class="info">
                    <H4>{{nombreDimension}}</H4>
                    {{ info }}
                </DIV>
            </l-control>

            <l-geo-json
                :geojson="datosMapa"
                :options="geojsonOptions"
            >
            </l-geo-json>
            <l-tile-layer :url="url"></l-tile-layer>
        </l-map>
    </div>
</template>

<script>

    import {Plotly} from 'vue-plotly';
    import numeral from 'numeral';
    import axios from 'axios';
    import {LMap, LTileLayer,LGeoJson, LControl } from 'vue2-leaflet';

    import GraficoMixin from '../Mixins/GraficoMixin';

    export default {
        components : { Plotly,LMap, LTileLayer, LGeoJson, LControl},
        mixins: [GraficoMixin],
        props: {
            indicador: Object,
            index: Number
        },
        data : function () {
            return {
                mapaDatosCargados : false,
                datosMapa : {},
                info: '',
                url: 'https://{s}.tile.osm.org/{z}/{x}/{y}.png',
                zoom: this.indicador.informacion.dimensiones[this.indicador.dimension].escala,
                center: [this.indicador.informacion.dimensiones[this.indicador.dimension].origenX, this.indicador.informacion.dimensiones[this.indicador.dimension].origenY],
                window_ : window,
                geojsonOptions: {
                    style: feature => {
                        let itemGeoJSONID = feature.properties.ID;
                        const data = this.indicador.data;

                        let item = data.find(x => x.id == itemGeoJSONID);
                        if (!item) { return { weight: 1, opacity: 0.6, color: "black"} };

                        return {
                            weight: 1,
                            opacity: 0.6,
                            color: "black",
                            dashArray: "3",
                            fillOpacity: 0.5,
                            fillColor: this.getColor(item.measure, this.indicador.informacion.rangos)
                        }
                    },
                    onEachFeature: (feature, layer) => {
                        layer.on({
                            mouseover: mouseover.bind(this),
                            mouseout: mouseout.bind(this),
                            click: click_.bind(this),
                            dblclick: doubleclick_.bind(this)
                        })
                    }
                }
            }
        },
        computed : {
            datos : function() {
                return this.datosMapa;
            }
        },
        mounted : function() {
            this.cargarDatosMapa();
        },
        methods: {
            cargarDatosMapa () {
                let nombre_mapa = this.indicador.informacion.dimensiones[this.indicador.dimension].mapa;
                let url = "/js/Mapas/"+nombre_mapa;
                let vm = this;

                axios.get( url )
                    .then( (response) => {

                        if (response.status == 200 ){
                            vm.datosMapa = response.data;
                            console.log(vm.datosMapa);
                            vm.mapaDatosCargados = true;
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                        vm.indicador.error = 'Error';

                    });
            },

            zoomUpdated (zoom) {
                console.log( 'Zoom: ' + zoom );
            },
            centerUpdated (center) {
                console.log('Center: ' + center);
            }
        },
        watch: {
            'indicador.full_screen': function () {
                this.$refs['myMap'+this.index].mapObject.invalidateSize();
            },

            'indicador.dimension': function () {
                this.cargarDatosMapa();
            }

        }
        
    }

    function mouseover({ target }) {
        target.setStyle({
            color: "blue",
            dashArray: ""
        });

        let itemGeoJSONID = target.feature.properties.ID;
        const data = this.indicador.data;
        let item = data.find(x => x.id == itemGeoJSONID);

        if ( item ) {
            this.info = item.category + ': ' + numeral( item.measure ).format('0,0.'+'0'.repeat(this.dec)) + this.indicador.informacion.unidad_medida;
        }

    }
    function mouseout({ target }) {
        target.setStyle({
            color: "black",
            dashArray: ""
        });
        this.info = "";
    }

    function click_({ target }) {

        let itemGeoJSONID = target.feature.properties.ID;
        const data = this.indicador.data;
        let item = data.find(x => x.id == itemGeoJSONID);

        this.click( {'points' : [{ 'x' : item.category}]} );
    }

    function doubleclick_ ({target}) {
        console.log("doble clic");
        this.doubleClickTime = Date.now();
        //this.$emit("doubleclick");
    }
</script>