<template>
    <div class="container-fluid row" >
            <div class=col-12>
                <b-card :title="$t('_filtrar_por_elemento_')"  footer-tag="footer">
                    <h5>{{ $t('_elija_elementos_mostrar_grafico_') }}</h5>
                    <b-list-group style="max-height: 40vh; min-height: 70px; overflow: auto;" >
                        <b-list-group-item 
                            
                            v-for="( item, index) in categorias" 
                            :key="index"
                            :style=" (indicador.otros_filtros.elementos.indexOf(item.category) > -1) ?  'background-color: #c0f4c0' : '' "
                            @click="agregarOtrosFiltros( item.category )" >
                            {{ item.category }}
                            <span class="badge badge-primary float-right " v-if="indicador.otros_filtros.elementos.indexOf(item.category) > -1 ">
                                <font-awesome-icon icon="check" />
                            </span>
                        </b-list-group-item>
                    </b-list-group>
                    <b-button slot="footer" variant="danger" @click="indicador.otros_filtros.elementos = [];" 
                        v-if="indicador.otros_filtros.elementos.length > 0">{{ $t('_quitar_filtro_') }}</b-button>
                </b-card>
            </div>
                        
        </div>
</template>

<script>

    export default {
        props: {
            indicador: Object,
            index: Number
        },        
        computed : {            
            categorias : function(){
                return this.indicador.data.sort((a, b) => a.category.localeCompare(b.category));
            }
        },

        methods : {            

            agregarOtrosFiltros : function ( valor) {
                if (this.indicador.otros_filtros.elementos.indexOf(valor) > -1) {
                    this.indicador.otros_filtros.elementos.splice(this.indicador.otros_filtros.elementos.indexOf(valor), 1 );
                } else {
                    this.indicador.otros_filtros.elementos.push(valor);
                }
            }
        }
    }
</script>