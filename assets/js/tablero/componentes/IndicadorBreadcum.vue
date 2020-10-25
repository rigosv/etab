<template>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb" style="background-color: #FFFFFF">
            <li class="breadcrumb-item" 
                v-for="(link, indexF) in indicador.filtros" 
                :key="indexF"
            >
                <a href="#" @click.prevent="breadcum(indexF)">{{ link.etiqueta.toUpperCase() }}: {{ link.valor }}</a>
            </li>
            <li class="breadcrumb-item" v-if="indicador.filtros.length == 0 " style="color: white">_</li>
        </ol>
    </nav> 
</template>

<script>
    import IndicadorMixin from '../Mixins/IndicadorMixin';

    export default {
        props: {
            indicador: {},
            index: Number,
        },
        mixins : [IndicadorMixin],        
        methods : {
            breadcum : function ( indexF ){
                //poner la nueva dimension
                this.indicador.dimension = this.indicador.filtros[indexF].codigo;
                this.indicador.filtros = this.indicador.filtros.slice(0, indexF);
                this.indicador.dimensionIndex = indexF;
                this.cargarDatosIndicador(this.indicador, this.index);
            }
        }
    }
</script>