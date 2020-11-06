<template>
    <div>
        <section class="content-header">       
            <b-navbar type="dark"  variant="secondary">
                <b-navbar-brand href="/">eTab</b-navbar-brand>
                <b-navbar-nav>
                    <b-nav-item href="#" v-if="!esSalaPublica">
                        <b-button v-b-modal.modalSalas variant="primary" style="padding: 5px;">
                            <font-awesome-icon icon="th" />
                            {{ $t("_salas_" ) }}
                        </b-button>
                    </b-nav-item>
                    <b-nav-item href="#" v-if="!esSalaPublica">
                        <b-button v-b-modal.modalIndicadores variant="success" style="padding: 5px;">
                            <font-awesome-icon icon="flag" />
                            {{ $t("_indicadores_" ) }}
                        </b-button>
                    </b-nav-item>                    
                </b-navbar-nav>
                <transition name="bounce" >
                    <b-navbar-nav class="ml-auto" v-if="$store.state.abrio_sala || $store.state.abrio_indicador">
                        <b-dropdown variant="info" :text="$t('_acciones_')" right>
                            <b-dropdown-item href="#" v-b-modal.modalExportar >
                                <font-awesome-icon icon="file-export" /> {{ $t("_exportar_" ) }}
                            </b-dropdown-item>
                            <b-dropdown-item href="#" v-b-modal.modalAccionesSala v-if="$store.state.abrio_sala && !esSalaPublica"  >
                                <font-awesome-icon icon="bookmark" /> {{ $t("_acciones_sala_situacional_" ) }}
                            </b-dropdown-item>
                            <b-dropdown-item href="#" v-b-modal.modalCompartirSala v-if="$store.state.abrio_sala && !esSalaPublica"  >
                                <font-awesome-icon icon="share" /> {{ $t("_compartir_sala_" ) }}
                            </b-dropdown-item>
                            <b-dropdown-item href="#" v-b-modal.modalFiltrosGenerales v-if="$store.state.abrio_sala && !esSalaPublica"  >
                                <font-awesome-icon icon="filter" /> {{ $t("_filtros_generales_" ) }}
                            </b-dropdown-item>
                        </b-dropdown>                    
                    </b-navbar-nav>
                </transition>
            </b-navbar>
        </section>
        
        <modal-salas></modal-salas>
        <modal-indicadores></modal-indicadores>
        <modal-exportar @convertir-graficos-sala="convertirGraficosSala" ></modal-exportar>
        <modal-acciones-sala></modal-acciones-sala>
        <modal-compartir-sala></modal-compartir-sala>
        <modal-filtros-generales></modal-filtros-generales>
    </div>
</template>

<script>
    import ModalSalas from './Modal/ModalSalas';
    import ModalIndicadores from './Modal/ModalIndicadores';
    import ModalExportar from './Modal/ModalExportar';
    import ModalAccionesSala from './Modal/ModalAccionesSala';
    import ModalCompartirSala from './Modal/ModalCompartirSala';
    import ModalFiltrosGenerales from './Modal/ModalFiltrosGenerales';

    export default {
        components : {ModalIndicadores, ModalSalas, ModalIndicadores, ModalExportar, 
                      ModalAccionesSala, ModalCompartirSala, ModalFiltrosGenerales },
        computed : {
            esSalaPublica : function() {
                return ( token != '' && idSala != '' );
            }
        },        
        methods : {
            convertirGraficosSala : function (){
                this.$emit('convertir-graficos-sala');
            }
        }
    }
</script>