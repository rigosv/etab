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
                            <vue-html2pdf
                                :show-layout="true"
                                :float-layout="false"
                                :enable-download="true"
                                :preview-modal="false"
                                :pdf-quality="1"
                                :manual-pagination="true"
                                :html-to-pdf-options = "pdfOptions"
                                pdf-content-width = "100%"
                                ref="tabla-datos-exportar-todas"
                            >
                                <section slot="pdf-content">
                                    <InfoTablaDatosContenido :indicador="indicador" v-for="(indicador, index_) in $store.state.indicadores" :key="index_"/>
                                </section>
                            </vue-html2pdf>
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
                            <vue-html2pdf
                                :show-layout="true"
                                :float-layout="false"
                                :enable-download="true"
                                :preview-modal="false"
                                :pdf-quality="1"
                                :manual-pagination="true"
                                :html-to-pdf-options = "pdfOptions"
                                pdf-content-width = "100%"
                                ref="ficha-tecnica-exportar-todas"
                            >
                                <section slot="pdf-content">
                                    <InfoFichaTecnicaContenido :indicador="indicador" v-for="(indicador, index_) in $store.state.indicadores" :key="index_"/>
                                </section>
                            </vue-html2pdf>
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
                            <vue-html2pdf
                                :show-layout="true"
                                :float-layout="false"
                                :enable-download="true"
                                :preview-modal="false"
                                :pdf-quality="1"
                                :manual-pagination="true"
                                :html-to-pdf-options = "pdfOptions"
                                pdf-content-width = "100%"
                                ref="export_graficos"
                            >
                                <section slot="pdf-content">
                                    <h3>{{ $store.state.sala.nombre }}</h3>
                                    <b-card class="col-12" header-tag="header" footer-tag="footer" 
                                        style="padding-left: 0px; padding-right: 0px; margin: 5px 0 5px 0; page-break-after: always;"
                                        v-for="(indicador, index_) in $store.state.indicadores" :key="index_"
                                    >
                                        <div slot="header" v-if="[ 'MAPA', 'GEOLOCATION', 'MAP' ].includes( indicador.configuracion.tipo_grafico.toUpperCase() )">
                                            <h4 style="font-size: 18px;">{{ indicador.nombre.toUpperCase() }}</h4>                                
                                        </div>

                                        <IndicadorBreadcum :indicador="indicador" :index="index_" />

                                        <img :id="'graph-export-' + indicador.index" />
                                        
                                        <div slot="footer">
                                            <div class="float-left" :title=" $t('_fecha_ultima_lectura_')" >[{{ indicador.informacion.ultima_lectura }}]</div>                  
                                            <div class="float-right">{{ $t('_meta_') }}: {{ indicador.informacion.meta }}</div>
                                        </div>                        
                                    </b-card>
                                </section>
                            </vue-html2pdf>                            
                        </DIV>
                    </div>
                </b-card-body>
            </b-card>
        </b-modal>        
    </div>
</template>

<script lang="ts">
import { Component, Vue } from "vue-property-decorator";
import VueHtml2pdf from 'vue-html2pdf';
import TableToExcel from '@linways/table-to-excel';

import InfoTablaDatosContenido from "../InfoTablaDatosContenido.vue";
import InfoFichaTecnicaContenido from "../InfoFichaTecnicaContenido.vue";
import IndicadorBreadcum from "../IndicadorBreadcum.vue";

@Component({
    components: { InfoTablaDatosContenido, InfoFichaTecnicaContenido, IndicadorBreadcum, VueHtml2pdf }
})
export default class ModalExportar extends Vue {
    
    mounted() {
        this.$root.$on('bv::modal::shown', (bvEvent:any, modalId:string) => {
            if ( modalId == 'modalExportar') {
                this.$emit('convertir-graficos-sala');                    
            }
        })
    }

    get pdfOptions() {
        return {      
        filename: this.$store.state.sala.nombre,
        margin: 0.5,
        image: { type: "jpeg", quality: 0.6 },
        html2canvas: { scale: 1 },
        jsPDF: { unit: "in", format: "letter", orientation: "portrait" },
        };
    }
    
    public exportarExcel(id:string, nombreArchivo:string): void {
        let vm = this;
        
        TableToExcel.convert(document.getElementById(id), {name:  vm.$t(nombreArchivo)+ ".xlsx"});
    }

    public exportarpdf(id: string, nombreArchivo: string):void {
        this.$refs[id].generatePdf();
    }

    public exportarcsv():void {
        let csvContent = "data:text/csv;charset=utf-8,";

        this.$store.state.indicadores.map ( (indicador:any) => {
            let arrData  = indicador.data;
            csvContent += [
                Object.keys(arrData[0]).join(","),
                ...arrData.map((item:any) => Object.values(item).join(","))
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
</script>