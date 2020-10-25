<template>
    <div>
        <div style="max-height:400px; max-width:100%; overflow:auto;"  >
            <div id="exportar_ficha_container">
                <InfoFichaTecnicaContenido :indicador="indicador" ></InfoFichaTecnicaContenido>
            </div>
        </div>
        <div style="padding-top: 10px;">
            <b-button size="sm" variant="outline-primary" @click="exportarExcel()">
                <font-awesome-icon icon="file-excel" />{{ $t('_exportar_excel_') }}
            </b-button>
            <b-button size="sm" variant="outline-primary" @click="exportarpdf()">
                <font-awesome-icon icon="file-pdf" />{{ $t('_exportar_pdf_') }}
            </b-button>
        </DIV>
    </div>
</template>

<script>

    import jsPDF from 'jspdf';
    import html2canvas from 'html2canvas';
    import html2pdf from 'html2pdf.js';
    import TableToExcel from '@linways/table-to-excel';

    import InfoFichaTecnicaContenido from './InfoFichaTecnicaContenido'
    import ColorMixin from "../Mixins/ColorMixin";

    export default {
        props: {
              indicador: Object
        },
        components : {InfoFichaTecnicaContenido},
        mixins : [ColorMixin],
        methods : {
            exportarExcel :  function () {
                let nombreArchivo = this.indicador.nombre + ' - '+this.$t('_ficha_tecnica_');                
                TableToExcel.convert(document.getElementById("exportar_ficha_container"), {name:nombreArchivo + ".xlsx"});
            },
            exportarpdf : function () {
                let nombreArchivo = this.indicador.nombre + ' - '+this.$t('_ficha_tecnica_');
                let ficha = document.getElementById('exportar_ficha_container');
                const opt = {
                    margin:       0.5,
                    filename:     nombreArchivo +'.pdf',
                    image:        { type: 'jpeg', quality: 0.98 },
                    html2canvas:  { scale: 1 },
                    jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
                };

                html2pdf().set(opt).from(ficha).save();
                
            }
        }

    }

    
</script>