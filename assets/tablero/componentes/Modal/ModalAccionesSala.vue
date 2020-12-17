<template>
  <b-modal
    id="modalAccionesSala"
    :title="$t('_acciones_sala_situacional_')"
    ok-only
    :ok-title="$t('_cancelar_')"
    ok-variant="secondary"
    size="lg"
  >
    <b-tabs content-class="mt-2">
      <b-tab :title="$t('_agregar_accion_')" active>
        <form id="acciones_frm" class="form-horizontal" role="form">
          <div class="form-group" id="acciones_div">
            <label for="acciones" class="col-sm-2 control-label col-lg-3">{{
              $t("_acciones_")
            }}</label>
            <div class="col-sm-10 col-lg-8">
              <textarea
                class="form-control"
                rows="3"
                id="acciones"
                v-model="accion.acciones"
              ></textarea>
            </div>
          </div>
          <div class="form-group">
            <label
              for="observaciones"
              class="col-sm-2 col-lg-3 control-label"
              >{{ $t("_observaciones_") }}</label
            >
            <div class="col-sm-10 col-lg-8">
              <textarea
                class="form-control"
                rows="3"
                id="observaciones"
                v-model="accion.observaciones"
              ></textarea>
            </div>
          </div>
          <div class="form-group">
            <label for="responsables" class="col-sm-2 control-label col-lg-3">{{
              $t("_responsables_")
            }}</label>
            <div class="col-sm-10 col-lg-8">
              <textarea
                class="form-control"
                rows="3"
                id="responsables"
                v-model="accion.responsables"
              ></textarea>
            </div>
          </div>

          <button
            class="btn btn-primary"
            type="button"
            @click="guardarAccionSala"
          >
            {{ $t("_guardar_") }}
          </button>
        </form>
      </b-tab>
      <b-tab
        :title="$t('_historial_sala_')"
        v-if="
          $store.state.sala_acciones != undefined &&
            $store.state.sala_acciones.length > 0
        "
      >
        <table class="table table-bordered table-striped">
          <thead>
            <tr class="sonata-ba-view-title" bgcolor="#E1EFFB">
              <th
                v-for="(value, key) in $store.state.sala_acciones[0]"
                :key="key"
              >
                {{ key }}
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(value, key) in $store.state.sala_acciones" :key="key">
              <td v-for="(v, k) in value" :key="k">{{ v }}</td>
            </tr>
          </tbody>
        </table>
      </b-tab>
    </b-tabs>
  </b-modal>
</template>

<script lang="ts">
import {Component, Vue} from 'vue-property-decorator';
import axios from 'axios';

@Component
export default class ModalAccionesSala extends Vue{
  private accion =  {acciones: '', responsables: '', observaciones: ''};


  public guardarAccionSala(): void {
      let vm = this;

      if (this.accion.acciones.trim() === '' ){
          this.$snotify.warning( this.$t("_debe_agregar_acciones_") as string);
      } else {
          let json = JSON.parse(JSON.stringify(this.accion));

          axios.post("/api/v1/tablero/salaAccion/" + vm.$store.state.sala.id, json )
              .then(function (response) {
                  if ( response.data.status == 200 ){
                      vm.$store.state.sala_acciones = response.data.data;
                      vm.accion = {'acciones': '', 'responsables': '', 'observaciones' : ''} ;
                      vm.$snotify.success(vm.$t('_guardar_ok_') as string);
                  } else {
                      vm.$snotify.error(vm.$t("_guardar_error_") as string, vm.$t("_error_") as string);
                  }
              });
      }
  }
};
</script>
