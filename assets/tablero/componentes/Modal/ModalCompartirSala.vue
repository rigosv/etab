<template>
  <b-modal
    id="modalCompartirSala"
    :title="$t('_compartirSala_')"
    ok-only
    :ok-title="$t('_cancelar_')"
    ok-variant="secondary"
    size="lg"
  >
    <b-tabs content-class="mt-2">
      <b-tab :title="$t('_usuarios_')" active>
        <form id="compartir_frm" class="form-horizontal" role="form">
          <div class="form-group" id="usuarios_div">
            <label for="acciones" class="col-sm-2 control-label col-lg-3">{{
              $t("_usuariosConCuenta_")
            }}</label>
            <div class="col-sm-12 col-lg-12">
              <v-select
                multiple
                :placeholder="$t('_elijaUsuariosACompartir_')"
                label="nombre"
                v-model="datos.usuarios_con_cuenta"
                :options="salasUsuario"
              />
            </div>
          </div>
          <div class="form-group">
            <label for="usuarios_sin" class="col-sm-2 col-lg-3 control-label">{{
              $t("_usuariosSinCuenta_")
            }}</label>
            <div class="col-sm-12 col-lg-12">
              <textarea
                class="form-control"
                :placeholder="$t('_usuarioSinCuentaCorreos_')"
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
              {{ $t("_esPermanente_") }}
            </b-form-checkbox>
          </div>
          <div class="form-group">
            <label for="tiempo_dias" class="col-sm-12 control-label">{{
              $t("_tiempoEnDias_")
            }}</label>
            <div class="col-sm-12 col-lg-12">
              <b-form-input
                v-model="datos.tiempo_dias"
                id="tiempo_dias"
                type="number"
              ></b-form-input>
            </div>
          </div>
        </form>
      </b-tab>
      <b-tab :title="$t('_comentarios_')">
        <form id="chat-form" class="form-horizontal" role="form">
          <div class="form-group">
            <label
              for="_comentarios_"
              class="col-sm-2 col-lg-3 control-label"
              >{{ $t("_comentarios_") }}</label
            >
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
          <b-card :header="$t('_foroDiscusion_')">
            <div
              class="comments-container"
              style="max-height: 375.2px; min-height: 70px; overflow: auto;"
            >
              <b-list-group>
                <b-list-group-item
                  class="d-flex align-items-center"
                  v-for="c in salaComentarios"
                  :key="c.id"
                >
                  <b-avatar
                    variant="info"
                    class="mr-3"
                    :text="c.nombre"
                  ></b-avatar>
                  <span class="mr-auto">{{ c.comentario }}</span>
                  <b-badge> {{ c.fecha }}</b-badge>
                </b-list-group-item>
              </b-list-group>
            </div>
          </b-card>
        </div>
      </b-tab>
      <button
        class="btn btn-primary"
        type="button"
        @click="guardarCompartirSala"
      >
        {{ $t("_guardar_") }}
      </button>
    </b-tabs>
  </b-modal>
</template>

<script lang="ts">
import {
  defineComponent,
  reactive,
  onMounted,
  computed
} from "@vue/composition-api";
import vSelect from "vue-select";

import EventService from "../../services/EventService";

export default defineComponent({
  components: { vSelect },

  setup(props, { root }) {
    const datos = reactive({
      usuarios_con_cuenta: [],
      usuarios_sin_cuenta: "",
      comentarios: "",
      correo: 0,
      tiempo_dias: 1,
      es_permanente: false
    });

    onMounted(() => {
      datos.usuarios_con_cuenta = root.$store.state.salaUsuarios.filter(
        (s: any) => {
          return s.selected == true;
        }
      );
    });

    const salasUsuario = computed(() => {
      return root.$store.state.salaUsuarios;
    });

    const salaComentarios = computed(() => {
      return root.$store.state.salaComentarios;
    });

    const guardarCompartirSala = () => {
      EventService.guardarSalaCompartir(root.$store.state.sala.id, datos).then(
        response => {
          if (response.data.status == 200) {
            root.$bvModal.hide("modalCompartirSala");
            root.$snotify.success(root.$t("_guardarOk_") as string);
            root.$store.state.salaComentarios.unshift(response.data.data);
          } else {
            root.$snotify.error(
              root.$t("_guardarError_") as string,
              root.$t("_error_") as string
            );
          }
        }
      );
    };

    return { datos, guardarCompartirSala, salasUsuario, salaComentarios };
  }
});
</script>
