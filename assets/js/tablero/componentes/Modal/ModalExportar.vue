<template>
    <div>
        <b-modal id="modalExportar" :title="$t('_exportar_') " ok-only size="lg">
            <b-card no-body class="overflow-hidden" >
                <b-card-body :title="$t('_tablas_datos_')">
                    <b-button variant="outline-secondary" @click="exportarExcel('tabla-datos-exportar-todas', '_tabla_datos_')">
                        <font-awesome-icon icon="file-excel" size="2x"/>
                        <br/>
                        {{ $t('_xls_') }}
                    </b-button>
                    <b-button variant="outline-success" @click="exportarpdf('tabla-datos-exportar-todas', $store.state.sala.nombre +'-'+$t('_tabla_datos_'))">
                        <font-awesome-icon icon="file-pdf" size="2x"/>
                        <br/>
                        {{ $t('_pdf_') }}
                    </b-button>
                    <b-button variant="outline-primary" @click="exportarcsv()">
                        <font-awesome-icon icon="file-csv" size="2x"/>
                        <br/>
                        {{ $t('_csv_') }}
                    </b-button>
                    <div style="display: none;">
                        <div id="tabla-datos-exportar-todas">
                            <InfoTablaDatosContenido :indicador="indicador" v-for="(indicador, index_) in $store.state.indicadores" :key="index_"/>
                        </div>
                    </div>
                </b-card-body>
            </b-card>
            <b-card no-body class="overflow-hidden" >
                <b-card-body :title="$t('_fichas_tecnicas_')">
                    <b-button variant="outline-secondary" @click="exportarExcel('ficha-tecnica-exportar-todas', '_ficha_tecnica_')">
                        <font-awesome-icon icon="file-excel" size="2x"/>
                        <br/>
                        {{ $t('_xls_') }}
                    </b-button>
                    <b-button variant="outline-success" @click="exportarpdf('ficha-tecnica-exportar-todas', $store.state.sala.nombre +'-'+$t('_ficha_tecnica_'))">
                        <font-awesome-icon icon="file-pdf" size="2x"/>
                        <br/>
                        {{ $t('_pdf_') }}
                    </b-button>
                    <div style="display: none;">
                        <div id="ficha-tecnica-exportar-todas" >
                            <InfoFichaTecnicaContenido :indicador="indicador" v-for="(indicador, index_) in $store.state.indicadores" :key="index_"/>
                        </div>
                    </div>
                </b-card-body>
            </b-card>

            <b-card no-body class="overflow-hidden" >
                <b-card-body :title="$t('_graficos_') ">
                    <b-button variant="outline-success"  @click="exportarpdf('export_graficos', $store.state.sala.nombre +'-'+ $t('_sala_'))">
                        <font-awesome-icon icon="file-pdf" size="2x"/>
                        <br/>
                        {{ $t('_pdf_') }}
                    </b-button>
                    <div style="display: none">
                        <DIV id='export_graficos'>
                            <h3>{{ $store.state.sala.nombre }}</h3>
                            <b-card class="col-12" header-tag="header" footer-tag="footer" 
                                style="padding-left: 0px; padding-right: 0px; margin: 5px 0 5px 0; page-break-after: always;"
                                v-for="(indicador, index_) in $store.state.indicadores" :key="index_"
                            >
                                <div slot="header" v-if="[ 'MAPA', 'GEOLOCATION', 'MAP' ].includes( indicador.configuracion.tipo_grafico.toUpperCase() )">
                                    <h4 style="font-size: 18px;">{{ indicador.nombre.toUpperCase() }}</h4>                                
                                </div>

                                <IndicadorBreadcum :indicador="indicador" :index="index_" />

                                <img :id="'graph-export-' + indicador.index" ></img>
                                
                                <div slot="footer">
                                    <div class="float-left" :title=" $t('_fecha_ultima_lectura_')" >[{{ indicador.informacion.ultima_lectura }}]</div>                  
                                    <div class="float-right">{{ $t('_meta_') }}: {{ indicador.informacion.meta }}</div>
                                </div>                        
                            </b-card>
                        </DIV>
                    </div>
                </b-card-body>
            </b-card>
        </b-modal>        
    </div>
</template>

<script>
    import jsPDF from 'jspdf';
    import html2canvas from 'html2canvas';
    import html2pdf from 'html2pdf.js';
    import TableToExcel from '@linways/table-to-excel';

    import InfoTablaDatosContenido from "../InfoTablaDatosContenido";
    import InfoFichaTecnicaContenido from "../InfoFichaTecnicaContenido";
    import IndicadorBreadcum from "../IndicadorBreadcum";

    export default {
        components: {InfoTablaDatosContenido, InfoFichaTecnicaContenido, IndicadorBreadcum},
        mounted() {
            this.$root.$on('bv::modal::shown', (bvEvent, modalId) => {
                if ( modalId == 'modalExportar') {
                    this.$emit('convertir-graficos-sala');                    
                }
            })
        },        
        methods : {
            exportarExcel :  function (id, nombreArchivo) {
                let vm = this;
                
                TableToExcel.convert(document.getElementById(id), {name:  vm.$t(nombreArchivo)+ ".xlsx"});
            },
            exportarpdf : function (id, nombreArchivo) {

                let html = document.getElementById(id);
                const opt = {
                    margin:       0.5,
                    filename:     nombreArchivo + '.pdf',
                    image:        { type: 'jpeg', quality: 0.98 },
                    html2canvas:  { scale: 1},
                    jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
                };

                html2pdf().set(opt).from(html).save();

            },
            exportarcsv : function(){
                let csvContent = "data:text/csv;charset=utf-8,";

                this.$store.state.indicadores.map ( indicador => {
                    let arrData  = indicador.data;
                    csvContent += [
                        Object.keys(arrData[0]).join(","),
                        ...arrData.map(item => Object.values(item).join(","))
                    ]
                        .join("\n")
                        .replace(/(^\[)|(\]$)/gm, "");
                    csvContent += '\n\n';
                });


                const data = encodeURI(csvContent);
                const link = document.createElement("a");
                link.setAttribute("href", data);
                link.setAttribute("download", this.$t('_tabla_datos_') + ".csv");
                link.click();
            }
        }
    }
</script>