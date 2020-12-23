<template>
  <table
    class="table table-bordered table-striped"
    data-cols-width="70,100"
    style="max-width:15.5cm"
  >
    <thead>
      <tr class="table-secondary">
        <th colspan="2" data-f-bold="true" data-fill-color="FFD6D8DB">
          {{ indicador.nombre }}
        </th>
      </tr>
      <tr class="sonata-ba-view-title" bgcolor="#E1EFFB">
        <th data-f-bold="true" data-fill-color="FFE1EFFB">
          {{ $t("_nombreCampo_") }}
        </th>
        <th data-f-bold="true" data-fill-color="FFE1EFFB">
          {{ $t("_descripcion_") }}
        </th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th data-f-bold="true">{{ $t("_nombre_") }}</th>
        <td>{{ indicador.ficha.nombre }}</td>
      </tr>
      <tr>
        <th data-f-bold="true">{{ $t("_interpretacion_") }}</th>
        <td>{{ indicador.ficha.tema }}</td>
      </tr>
      <tr>
        <th data-f-bold="true">{{ $t("_concepto_") }}</th>
        <td>{{ indicador.ficha.concepto }}</td>
      </tr>
      <tr>
        <th data-f-bold="true">{{ $t("_unidadMedida_") }}</th>
        <td>{{ indicador.ficha.unidad_medida }}</td>
      </tr>
      <tr>
        <th data-f-bold="true">{{ $t("_acumulable_") }}</th>
        <td>{{ indicador.ficha.es_acumulado ? "SI" : "NO" }}</td>
      </tr>
      <tr>
        <th data-f-bold="true">{{ $t("_observaciones_") }}</th>
        <td>{{ indicador.ficha.observacion }}</td>
      </tr>
      <tr>
        <th data-f-bold="true">{{ $t("_clasificacionTecnica_") }}</th>
        <td>
          <ul class="list-group">
            <li
              class="list-group-item"
              v-for="(v, k) in indicador.ficha.clasificacionTecnica"
              :key="k"
            >
              {{ v.descripcion }}
            </li>
          </ul>
        </td>
      </tr>
      <tr>
        <th data-f-bold="true">{{ $t("_meta_") }}</th>
        <td>{{ indicador.ficha.meta }}</td>
      </tr>
      <tr>
        <th data-f-bold="true">{{ $t("_periocidad_") }}</th>
        <td>
          {{
            indicador.ficha.periodo != null
              ? indicador.ficha.periodo.descripcion
              : ""
          }}
        </td>
      </tr>
      <tr>
        <th data-f-bold="true">{{ $t("_confiabilidad_") }}</th>
        <td>{{ indicador.ficha.confiabilidad }}</td>
      </tr>
      <tr>
        <th data-f-bold="true">{{ $t("_ultimaLectura_") }}</th>
        <td>{{ indicador.ficha.ultima_lectura }}</td>
      </tr>

      <tr>
        <th data-f-bold="true">{{ $t("_camposIndicador_") }}</th>
        <td>{{ indicador.ficha.dimensiones }}</td>
      </tr>
      <tr>
        <th data-f-bold="true">{{ $t("_formula_") }}</th>
        <td>{{ indicador.ficha.formula }}</td>
      </tr>

      <tr>
        <th colspan="2">
          <h4>{{ $t("_variables_") }}</h4>
          <table
            class="table table-bordered table-striped"
            v-for="(v, k) in indicador.ficha.variables"
            :key="k"
          >
            <caption style="caption-side: top">
              Var #
              {{
                k + 1
              }}
            </caption>
            <tbody>
              <tr>
                <th width="20%" data-f-bold="true">
                  {{ $t("_confiabilidad_") }}
                </th>
                <td>{{ v.confiabilidad }}</td>
              </tr>
              <tr>
                <th data-f-bold="true">{{ $t("_nombre_") }}</th>
                <td>{{ v.nombre }}</td>
              </tr>
              <tr>
                <th data-f-bold="true">{{ $t("_iniciales_") }}</th>
                <td>{{ v.iniciales }}</td>
              </tr>
              <tr>
                <th data-f-bold="true">{{ $t("_fuente_") }}</th>
                <td>
                  {{
                    v.fuente_dato
                      ? v.fuente_dato.establecimiento +
                        "(" +
                        v.fuente_dato.contacto +
                        ")"
                      : ""
                  }}
                </td>
              </tr>
              <tr>
                <th data-f-bold="true">{{ $t("_origenDatos_") }}</th>
                <td>{{ v.origen_dato ? v.origen_dato.nombre : "" }}</td>
              </tr>
              <tr>
                <th data-f-bold="true">{{ $t("_nombreConexion_") }}</th>
                <td>
                  <ul class="list-group">
                    <li
                      class="list-group-item"
                      v-for="(v1, k1) in v.origen_dato.conexion"
                      :key="k1"
                    >
                      {{ v1.nombre }} ({{ v1.ip }})
                    </li>
                  </ul>
                </td>
              </tr>
              <tr>
                <th data-f-bold="true">{{ $t("_responsableDatos_") }}</th>
                <td>
                  {{
                    v.responsable_dato
                      ? v.responsable_dato.establecimiento +
                        "(" +
                        v.responsable_dato.contacto +
                        ")"
                      : ""
                  }}
                </td>
              </tr>
            </tbody>
          </table>
        </th>
      </tr>
      <tr v-if="indicador.ficha.alertas.length > 0">
        <th colspan="2">
          <table class="table table-bordered table-striped">
            <caption style="caption-side: top">
              {{
                $t("_alertas_")
              }}
            </caption>
            <thead>
              <tr class="sonata-ba-view-title" bgcolor="#E1EFFB">
                <th data-f-bold="true">{{ $t("_color_") }}</th>
                <th data-f-bold="true">{{ $t("_rango_") }}</th>
                <th data-f-bold="true">{{ $t("_comentarios_") }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(v, k) in indicador.ficha.alertas" :key="k">
                <td
                  :style="{ background: v.color.codigo }"
                  :data-fill-color="getColorExceljs(v.color.codigo)"
                >
                  {{ v.color.color }}
                </td>
                <td>{{ v.limite_inf }} - {{ v.limite_sup }}</td>
                <td>{{ v.comentario }}</td>
              </tr>
            </tbody>
          </table>
        </th>
      </tr>
    </tbody>
  </table>
</template>

<script lang="ts">
import { defineComponent } from "@vue/composition-api";
import useColor from "../compositions/useColor";

export default defineComponent({
  props: {
    indicador: { default: {}, type: Object }
  },

  setup() {
    return { ...useColor() };
  }
});
</script>
