<template>
  <div>
    <Plotly
      :id="'grafico-' + index"
      ref="grafico"
      :data="datos"
      :layout="layout"
      :display-mode-bar="false"
      v-if="indicador.data.length > 0"
      @click="click"
      @doubleclick="doubleclick"
      @selected="selected"
      @deselect="deselect"
    >
    </Plotly>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "@vue/composition-api";
import { Plotly } from "vue-plotly";
import numeral from "numeral";

import useGrafico from "../compositions/useGrafico";
import useColor from "../compositions/useColor";
import useClicEvents from "../compositions/useClicEvents";
import useCargadorDatos from "../compositions/useCargadorDatos";

export default defineComponent({
  components: { Plotly },
  props: {
    indicador: { default: {}, type: Object },
    index: Number
  },

  setup(props, { root, emit }) {
    return {
      ...useCargadorDatos(root),
      ...useGrafico(props.indicador),
      ...useColor(),
      ...useClicEvents(props.indicador, emit)
    };
  },

  computed: {
    tipoGrafico(): string {
      let resp = "";
      if (
        ["BARRA", "BARRAS", "COLUMNAS", "COLUMNA", "DISCRETEBARCHART"].includes(
          this.indicador.configuracion.tipo_grafico.toUpperCase()
        )
      ) {
        resp = "bar";
      } else if (
        ["LINECHART", "LINEA", "LINEAS"].includes(
          this.indicador.configuracion.tipo_grafico.toUpperCase()
        )
      ) {
        resp = "scatter";
      } else if (
        ["PIECHART", "PIE", "PASTEL", "TORTA"].includes(
          this.indicador.configuracion.tipo_grafico.toUpperCase()
        )
      ) {
        resp = "pie";
      } else if (
        ["BOX"].includes(
          this.indicador.configuracion.tipo_grafico.toUpperCase()
        )
      ) {
        resp = "box";
      } else if (
        ["BURBUJA"].includes(
          this.indicador.configuracion.tipo_grafico.toUpperCase()
        )
      ) {
        resp = "burbuja";
      }
      return resp;
    },

    datos(): any {
      let traces = [];

      if (this.indicador.tendencia) {
        const groups = this.indicador.data_tendencia.reduce(
          (groups: any, item: any) => {
            const group = groups[item.category] || [];
            group.push(item);
            groups[item.category] = group;
            return groups;
          },
          {}
        );

        const keys = Object.keys(groups);

        for (const categoria of keys) {
          const x = groups[categoria].map((f: any) => {
            return f.fecha;
          });
          const y = groups[categoria].map((f: any) => {
            return f.measure;
          });

          traces.push({
            x: x,
            y: y,
            type: "scatter",
            text: y.map((v: any) =>
              numeral(v).format("0,0." + "0".repeat(this.dec))
            ),
            textposition: "auto",
            hoverinfo: "text",
            hovertemplate: "%{x}<br><b>%{y:,}</b>",
            showlegend: true,
            name: categoria
          });
        }
      } else {
        if (this.indicador.configuracion.dimensionComparacion == "") {
          const trace0 = this.getDataTrace(this.datosOrdenados, 1);
          traces.push(trace0);
          if (
            this.indicador.dataComparar.length == 0 &&
            this.indicador.informacion.meta != null
          ) {
            traces.push(this.agregarMeta());
          }

          if (
            this.tipoGrafico != "burbuja" &&
            this.indicador.dataComparar.length > 0
          ) {
            let index_ = 0;
            for (const ind of this.indicador.dataComparar) {
              let data_ = ind.data.map((f: any) => {
                return { x: f.category, y: f.measure };
              });

              //Incluir solo los elementos que también existan en el indicador principal
              const filtros_ = this.indicador.otros_filtros.elementos;
              if (filtros_.length > 0) {
                data_ = data_.filter((d: any) => {
                  return filtros_.includes(d.x);
                });
              }

              traces.push(
                this.getDataTrace(this.aplicarOrden(data_), index_++ + 2)
              );
            }
          }
        } else {
          traces = this.datosComparacionDimension();
          if (this.indicador.informacion.meta != null) {
            traces.push(this.agregarMeta());
          }
        }
      }

      return traces;
    },

    layout(): any {
      const titulo =
        this.indicador.nombre.match(/.{1,40}/g) != null &&
        this.indicador.dataComparar.length == 0
          ? this.indicador.nombre.match(/.{1,40}/g).join("<BR>")
          : "";
      const height_ = this.indicador.full_screen
        ? window.innerHeight / 1.15
        : parseFloat(this.indicador.configuracion.layout.h) * 30 - 100;

      const layout_: any = {
        title: titulo,
        height: height_,
        autosize: true,
        yaxis: {
          title: this.indicador.informacion.unidad_medida,
          exponentformat: "none"
        },
        xaxis: {
          title: this.nombreDimension,
          exponentformat: "none",
          type: "category"
        }
      };

      if (this.tipoGrafico == "pie") {
        layout_.margin = { l: 30, r: 30, b: 30, t: 30, pad: 10 };
      } else {
        layout_.dragmode = "select";
        layout_.selectdirection = "h";
        layout_.margin = { l: 50, r: 5, b: 50, t: 30, pad: 4 };
      }
      if (this.indicador.configuracion.dimensionComparacion != "") {
        layout_.yaxis.fixedrange = layout_.xaxis.fixedrange = true;
      }

      return layout_;
    }
  },

  methods: {
    agregarMeta(): any {
      const meta = this.indicador.informacion.meta;
      if (meta != null) {
        const category = [...new Set(this.datosOrdenados.map((d: any) => d.x))];
        const max = category[category.length - 1];
        const min = category[0];
        return {
          x: [min, max],
          y: [meta, meta],
          type: "scatter",
          mode: "lines",
          textposition: "bottom",
          showlegend: false,
          hoverinfo: "none",
          name: this.$t("_meta_"),
          hovertemplate: "%{y:,}",
          line: {
            color: "black",
            dash: "dash"
          }
        };
      }
    },

    getDataTrace(data_: any, pos: number): any {
      const x = data_.map((f: any) => {
        return f.x;
      });
      const y = data_.map((f: any) => {
        return f.y;
      });
      //Asignar un color a cada elemento X y mantenerlo aunque se hayan filtrado, para que al redibujar el gráfico filtrado no le cambie de color al elemento del gráfico
      const rangos =
        pos == 1
          ? this.indicador.informacion.rangos
          : this.indicador.dataComparar[pos - 2].informacion.rangos;
      const colores = this.getColores(this.indicador.data, rangos)
        .filter((c: any) => {
          return x.includes(c.x);
        })
        .map((c: any) => {
          return c.color;
        });
      const trace = {};
      //El tipo burbuja solo se mostrará cuando se estén comparando indicadores
      // El eje y será el valor de un indicador y el diámetro de la burbuja será el valor del otro indicador
      // solo se utilizará al comparar dos indicadores, si hay más no se tomarán en cuenta
      if (
        this.tipoGrafico == "burbuja" &&
        this.indicador.dataComparar.length > 0
      ) {
        //Buscar el tamaño de la burbuja en el otro indicador a comparar
        let size_ = x.map((x_: any) => {
          return this.indicador.dataComparar[0].data.find((d: any) => {
            return d.category == x_;
          });
        });
        //Asignar 1 a los valores que no tengan correspondencia en el paso anterior, y escalarlos
        size_ = size_.map((v: any) => {
          return v == undefined ? 1 : parseFloat(v.measure);
        });
        //Escalar los datos, 2000 será el mayor tamaño de burbuja
        const factor = 2000 / Math.max.apply(null, size_);
        const size = size_.map((v: any) => {
          return v * factor;
        });

        const nombre = this.indicador.dataComparar[0].nombre;
        const nombreIndC = this.indicador.full_screen
          ? nombre
          : nombre.substring(0, 30) + (nombre.length > 0 ? "..." : "");

        const dec = isNaN(this.indicador.ficha.cantidad_decimales)
          ? 2
          : this.indicador.ficha.cantidad_decimales;
        const texto = size_.map((v: any) => {
          return (
            "(" +
            numeral(v).format("0,0." + "0".repeat(this.dec)) +
            ") " +
            nombreIndC
          );
        });

        return {
          x: x,
          y: y,
          text: texto,
          mode: "markers",
          marker: {
            color: colores,
            size: size,
            sizemode: "area"
          }
        };
      } else if (this.tipoGrafico == "pie") {
        return {
          labels: x,
          values: y,
          type: "pie",
          textinfo: "label+value",
          hoverinfo: "label+value",
          showlegend: false
        };
      } else if (this.tipoGrafico == "box") {
        return {
          y: y,
          type: "box",
          boxmean: "sd",
          boxpoints: "all",
          jitter: 0.3,
          whiskerwidth: 0.2,
          fillcolor: "cls",
          marker: {
            size: 4
          },
          line: {
            width: 1
          },
          showlegend: this.indicador.dataComparar.length > 0,
          name:
            pos +
            " - " +
            (pos == 1
              ? this.indicador.nombre
              : this.indicador.dataComparar[pos - 2].nombre)
        };
      } else {
        return {
          x: x,
          y: y,
          type: this.tipoGrafico,
          text: y.map((v: any) =>
            numeral(v).format("0,0." + "0".repeat(this.dec))
          ),
          textposition: "auto",
          hoverinfo: "label+value",
          hovertemplate: "%{x}<br><b>%{y:,}</b>",
          marker: { opacity: 0.6, color: colores, size: 14 },
          showlegend: this.indicador.dataComparar.length > 0,
          name:
            pos +
            ".- " +
            (pos == 1
              ? this.indicador.nombre
              : this.indicador.dataComparar[pos - 2].nombre)
        };
      }
    },

    downloadImage(options: any): void {
      (this.$refs.grafico as Vue & {
        downloadImage: (options: any) => any;
      }).downloadImage(options);
    },

    react(): void {
      (this.$refs.grafico as Vue & {
        schedule: (options: any) => any;
      }).schedule({
        replot: true
      });
    },

    toImage(options: any): any {
      (this.$refs.grafico as Vue & { toImage: (options: any) => any }).toImage(
        options
      );
    },

    datosComparacionDimension(): any {
      const traces: any[] = [];

      const subcategorias = [
        ...new Set(this.indicador.data.map((d: any) => d.subcategory))
      ];

      const rangos = this.indicador.informacion.rangos;
      subcategorias.map((sc, index) => {
        let serie = this.indicador.data.filter((d: any) => d.subcategory == sc);
        serie =
          this.indicador.configuracion.orden_x == "desc"
            ? serie.sort((a: any, b: any) =>
                isNaN(a.category) || isNaN(b.category)
                  ? b.category.localeCompare(a.category)
                  : b.category - a.category
              )
            : serie.sort((a: any, b: any) =>
                isNaN(a.category) || isNaN(b.category)
                  ? a.category.localeCompare(b.category)
                  : a.category - b.category
              );

        const x = serie.map((s: any) => s.category);
        const y = serie.map((s: any) => s.measure);

        const dataSerie: any = {
          x: x,
          y: y,
          type: this.tipoGrafico,
          text: y.map((v: any) =>
            numeral(v).format("0,0." + "0".repeat(this.dec))
          ),
          textposition: "auto",
          hoverinfo: "text",
          hovertemplate: "%{x}<br><b>%{y:,}</b>",
          marker: { opacity: 0.6, size: 14 },
          showlegend: true,
          name: "<B>" + (index + 1) + ".- </B> " + sc
        };

        if (rangos.length > 0) {
          dataSerie.marker.color = y.map((v: any) => this.getColor(v, rangos));
        }

        traces.push(dataSerie);
      });

      return traces;
    }
  }
});
</script>
