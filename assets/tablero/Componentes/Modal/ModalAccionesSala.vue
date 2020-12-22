<template>
  <b-modal
    id="modalAccionesSala"
    :title="$t('_accionesSalaSituacional_')"
    ok-only
    :ok-title="$t('_cancelar_')"
    ok-variant="secondary"
    size="lg"
  >
    <b-tabs content-class="mt-2">
      <b-tab :title="$t('_agregarAccion_')" active>
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
        :title="$t('_historialSala_')"
        v-if="
          $store.state.salaAcciones != undefined &&
            $store.state.salaAcciones.length > 0
        "
      >
        <table class="table table-bordered table-striped">
          <thead>
            <tr class="sonata-ba-view-title" bgcolor="#E1EFFB">
              <th
                v-for="(value, key) in $store.state.salaAcciones[0]"
                :key="key"
              >
                {{ key }}
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(value, key) in $store.state.salaAcciones" :key="key">
              <td v-for="(v, k) in value" :key="k">{{ v }}</td>
            </tr>
          </tbody>
        </table>
      </b-tab>
    </b-tabs>
  </b-modal>
</template>

<script lang="ts">
import { defineComponent, reactive } from "@vue/composition-api";

import EventService from "../../services/EventService";

export default defineComponent({
  setup(props, { root }) {
    const accion = reactive({
      acciones: "",
      responsables: "",
      observaciones: ""
    });

    const guardarAccionSala = () => {
      if (accion.acciones.trim() === "") {
        root.$snotify.warning(root.$t("_debeAgregarAcciones_") as string);
      } else {
        //const json = JSON.parse(JSON.stringify(accion));

        EventService.guardarSalaAccion(root.$store.state.sala.id, accion).then(
          response => {
            if (response.data.status == 200) {
              root.$store.state.salaAcciones = response.data.data;
              accion.acciones = "";
              accion.responsables = "";
              accion.observaciones = "";
              root.$snotify.success(root.$t("_guardarOk_") as string);
            } else {
              root.$snotify.error(
                root.$t("_guardarError_") as string,
                root.$t("_error_") as string
              );
            }
          }
        );
      }
    };

    return { accion, guardarAccionSala };
  }
});
</script>
