<template>
  <b-modal id="modalCompartirSala" :title="$t('_compartir_sala_') " 
        ok-only 
        :ok-title="$t('_cancelar_')"  
        ok-variant="secondary" 
        size="lg"
  >
    <b-tabs content-class="mt-2">
        <b-tab :title="$t('_usuarios_')" active >
            <form id="compartir_frm" class="form-horizontal" role="form">
                <div class="form-group" id="usuarios_div">                            
                  <label for="acciones" class="col-sm-2 control-label col-lg-3" >{{ $t('_usuarios_con_cuenta_')  }}</label>
                  <div class="col-sm-12 col-lg-12">
                    <v-select
                        multiple
                        :placeholder="$t('_elija_usuarios_a_compartir_')"
                        label="nombre"
                        v-model="datos.usuarios_con_cuenta"
                        :options="$store.state.sala_usuarios"
                      />
                  </div>
                </div>
                <div class="form-group">
                  <label for="usuarios_sin" class="col-sm-2 col-lg-3 control-label" >{{ $t('_usuarios_sin_cuenta_') }}</label>
                  <div class="col-sm-12 col-lg-12">
                    <textarea
                      class="form-control"
                      :placeholder="$t('_usuario_sin_cuenta_correos_')"
                      rows="3"
                      id="usuarios_sin"
                      v-model="datos.usuarios_sin_cuenta"
                    ></textarea>
                  </div>
                </div>
                <div class="form-group col-sm-12 col-lg-12">
                    <b-form-checkbox
                      id="checkbox-1"
                      v-model="datos.es_permanente"
                      :value="true"
                      :unchecked-value="false"
                    >
                      {{ $t('_es_permanente_') }}
                    </b-form-checkbox>
                </div>
                <div class="form-group">
                  <label for="tiempo_dias" class="col-sm-12 control-label" >{{ $t('_tiempo_en_dias_') }}</label>
                  <div class="col-sm-12 col-lg-12">
                    <b-form-input v-model="datos.tiempo_dias" id="tiempo_dias" type="number"></b-form-input>
                  </div>
                </div>                
              </form>
        </b-tab>
        <b-tab :title="$t('_comentarios_')" >
            <form id="chat-form" class="form-horizontal" role="form">
                <div class="form-group">
                  <label for="_comentarios_" class="col-sm-2 col-lg-3 control-label" >{{ $t('_comentarios_') }}</label>
                  <div class="col-sm-12 col-lg-12">
                    <textarea
                      class="form-control"
                      rows="3"
                      id="_comentarios_"
                      v-model="datos.comentarios"
                    ></textarea>
                  </div>
                </div>
            </form>
            <div class="form-group col-sm-12 col-lg-12">
              <b-card
                :header="$t('_foro_discusion_')"
              >
                <div class="comments-container" style="max-height: 375.2px; min-height: 70px; overflow: auto;">                    
                    <b-list-group style="max-width: 300px;">
                      <b-list-group-item class="d-flex align-items-center"
                        v-for="c in $store.state.sala_comentarios" :key="c.id"
                      >
                        <b-avatar variant="info" class="mr-3" :text="c.nombre"></b-avatar>
                        <span class="mr-auto">{{c.comentario }}</span>
                        <b-badge> {{ c.fecha }}</b-badge>
                      </b-list-group-item>                      
                    </b-list-group>
                </div>
              </b-card>
            </div>
        </b-tab>
        <button class="btn btn-primary" type="button" @click="guardarCompartirSala" > {{ $t('_guardar_')  }} </button>
    </b-tabs>
  </b-modal>
</template>

<script lang="ts">
import {Component, Vue} from "vue-property-decorator";
import axios from 'axios';
import vSelect from 'vue-select';

@Component({
  components : { vSelect }
})
export default  class ModalCompartirSala extends Vue {
  private datos = {
                  usuarios_con_cuenta: [],
                  usuarios_sin_cuenta: "",
                  comentarios: "",
                  correo: 0,
                  tiempo_dias: 1,
                  es_permanente: false
                }
    mounted () {
      this.datos.usuarios_con_cuenta = this.$store.state.sala_usuarios.filter( (s:any) => { return s.selected == true });
    }
    
    public guardarCompartirSala(): void {
      let vm = this;
      
      let json = JSON.parse(JSON.stringify(this.datos));
      
      axios.post("/api/v1/tablero/comentarioSala/" + vm.$store.state.sala.id, json )
          .then(function (response) {
              if ( response.data.status == 200 ){
                  vm.$bvModal.hide('modalCompartirSala');
                  vm.$snotify.success(vm.$t('_guardar_ok_') as string);
              } else {
                  vm.$snotify.error(vm.$t("_guardar_error_") as string, vm.$t("_error_") as string);
              }
          });
                
    }    
};
</script>