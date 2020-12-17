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
        :class="{ active: sala_activa == item.id }"
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
            :disabled="sala_cargando"
          >
            <font-awesome-icon icon="sync" spin v-if="sala_cargando" />
            {{ $t("_borrar_") }}
          </button>
        </div>
      </li>
    </ul>
  </div>
</template>

<script lang="ts">
import { Component, Vue, Prop } from "vue-property-decorator";
import axios from "axios";

@Component
export default class ListadoSalas extends Vue {
  @Prop({ default: [] }) readonly salas!: any[];
  @Prop({ default: false }) readonly borrar!: boolean;

  private sala_cargando: boolean = false;
  private sala_activa: number = 0;

  public activarSala(sala: any): void {
    this.sala_activa = sala.id;
    this.$bvModal.hide("modalSalas");
    this.$emit("activarSala", sala);
  }

  public confirmarBorrarSala(sala: any): void {
    let vm = this;

    vm.$snotify.confirm(
      vm.$t("_esta_seguro_borrar_sala_") as string,
      vm.$t("_confirmar_") as string,
      {
        closeOnClick: false,
        //position: "centerCenter",
        backdrop: 0.7,
        buttons: [
          {
            text: vm.$t("_si_") as string,
            action: (toast) => {
              let json = { id: sala.id };
              axios
                .post("/api/v1/tablero/borrarSala", json)
                .then(function(response) {
                  if (response.data.status == 200) {
                    vm.$snotify.remove(toast.id);
                    vm.$emit("borrarSala", sala);
                    vm.$snotify.success(
                      vm.$t("_mensaje_ok_eliminar_sala_") as string
                    );
                  } else {
                    vm.$snotify.remove(toast.id);
                    vm.$snotify.error(
                      vm.$t("_mensaje_error_eliminar_sala_") as string,
                      vm.$t("_error_") as string,
                      { timeout: 10000 }
                    );
                  }
                });
            },
            bold: false,
          },
          {
            text: vm.$t("_cancelar_") as string,
            action: (toast) => {
              vm.$snotify.remove(toast.id);
            },
            bold: false,
          },
        ],
      }
    );
  }
}
</script>
