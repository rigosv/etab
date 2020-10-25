<template>
     <div >
         <b-alert
                 v-for=" m,k in mensajes"
                 :key="k"
                 show dismissible fade :variant="m.variante"
                  :show="indicador.error == m.error && dismissCountDown"
                  @dismissed="dismissCountDown=0; indicador.error=''"
                  @dismiss-count-down="countDownChanged"
         >
             {{ m.mensaje }}
             <b-progress
                     animated
                     :max="dismissSecs"
                     :value="dismissCountDown"
                     height="4px"
             ></b-progress>
         </b-alert>

         <b-alert show variant="info"
                  :show="(indicador.radial || indicador.termometro) && !indicador.informacion.meta"
         >
             {{ $t('_grafico_aprecia_mejor_meta_') }}
         </b-alert>
      </div>
</template>

<script>
    export default {
        props: {
            indicador: {},
            index: Number
        },
        data() {
            return {
                dismissSecs: 10,
                dismissCountDown: 0,
                showDismissibleAlert: false,
                mensajes : [
                    {variante : 'success', error: 'Success', mensaje: this.$t('_indicador_dimension_fin_')},
                    {variante : 'warning', error: 'Warning', mensaje: this.$t('_indicador_warning_')},
                    {variante : 'danger', error: 'Error', mensaje: this.$t('_indicador_error_')}
                ]
            }
        },
        methods: {
            countDownChanged(dismissCountDown) {
                this.dismissCountDown = dismissCountDown
            }
        },
        watch : {
            'indicador.error' : function () {
                if (this.indicador.mensaje != '') {
                    this.dismissCountDown = this.dismissSecs
                }
            }
        }
    }
</script>