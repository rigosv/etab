<template>
  <div class="col-sm-12">
    <ul
      class="list-group"
      style="max-height: 40vh; min-height: 70px; overflow: auto;"
      v-if="salas.length > 0"
    >
      <li
        class="list-group-item list-group-item-action list_indicador d-flex justify-content-between align-items-center"
        v-for="(item, key) in salas"
        :key="key"
        :class="{ active: salaActiva == item.id }"
        style="min-height: 54px;"
      >
        <A
          href="#"
          @click.prevent="activarSala(item)"
          style="width:100%; color: black;"
        >
          <span style="float:left; display:block; width: 90%">
            {{ item.nombre.toUpperCase() }}
          </span>
          <span
            class="pull-right badge badge-primary badge-pill"
            v-if="item.indicadores.length > 0 && !borrar"
            :title="$t('_indicadores_')"
          >
            {{ item.indicadores.length }}
          </span>
        </A>

        <div
          v-if="borrar"
          style="float:right"
          class="btn-group"
          role="group"
          aria-label="br"
        >
          <button
            type="button"
            class="btn btn-danger"
            @click.prevent="confirmarBorrarSala(item)"
            :disabled="salaCargando"
          >
            <font-awesome-icon icon="sync" spin v-if="salaCargando" />
            {{ $t("_borrar_") }}
          </button>
        </div>
      </li>
    </ul>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "@vue/composition-api";
import axios from "axios";

export default defineComponent({
  props: {
    salas: { default: [], type: Array },
    borrar: { default: false, type: Boolean }
  },

  data: () => ({
    salaCargando: false,
    salaActiva: 0
  }),

  methods: {
    activarSala(sala: any): void {
      this.salaActiva = sala.id;
      this.$bvModal.hide("modalSalas");
      this.$emit("activarSala", sala);
    },

    confirmarBorrarSala(sala: any): void {
      this.$snotify.confirm(
        this.$t("_estaSeguroBorrarSala_") as string,
        this.$t("_confirmar_") as string,
        {
          closeOnClick: false,
          //position: "centerCenter",
          backdrop: 0.7,
          buttons: [
            {
              text: this.$t("_si_") as string,
              action: (toast: any) => {
                const json = { id: sala.id };
                axios
                  .post("/api/v1/tablero/borrarSala", json)
                  .then(response => {
                    if (response.data.status == 200) {
                      this.$snotify.remove(toast.id);
                      this.$emit("borrarSala", sala);
                      this.$snotify.success(
                        this.$t("_mensajeOkEliminarSala_") as string
                      );
                    } else {
                      this.$snotify.remove(toast.id);
                      this.$snotify.error(
                        this.$t("_mensajeErrorEliminarSala_") as string,
                        this.$t("_error_") as string,
                        { timeout: 10000 }
                      );
                    }
                  });
              },
              bold: false
            },
            {
              text: this.$t("_cancelar_") as string,
              action: (toast: any) => {
                this.$snotify.remove(toast.id);
              },
              bold: false
            }
          ]
        }
      );
    }
  }
});
</script>
