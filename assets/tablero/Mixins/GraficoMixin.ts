import { defineComponent } from "@vue/composition-api";

export default defineComponent({
  data: () => ({
    doubleClickTime: 0,
    doubleClickThreshold: 500,
    seleccionActiva: false
  }),

  computed: {
    dec(): any {
      return isNaN(this.indicador.ficha.cantidad_decimales) ||
        this.indicador.ficha.cantidad_decimales == null
        ? 2
        : this.indicador.ficha.cantidad_decimales;
    },

    nombreDimension(): string {
      return this.indicador.dimension != undefined &&
        this.indicador.informacion.dimensiones != undefined
        ? this.indicador.informacion.dimensiones[
            this.indicador.dimension
          ].descripcion.toUpperCase()
        : "";
    },

    datosOrdenados(): any {
      const datos_ = this.indicador.data.map((f: any) => {
        return { x: f.category, y: f.measure };
      });

      return this.aplicarOrden(datos_);
    }
  },

  methods: {
    getColores(datos_: any, rangos: any): any {
      let indice = 0;
      const colores_ = this.$store.state.colores_;

      return datos_.map((f: any) => {
        let color = this.getColor(f.measure, rangos);

        if (color == "white") {
          color = colores_[indice];
          indice = indice == colores_.length - 1 ? 0 : indice + 1;
        }

        return { x: f.category, color: color };
      });
    },

    getColor(valor: number, rangos: any): string {
      let color = "";
      for (const rango of rangos) {
        if (valor >= rango.limite_inf && valor <= rango.limite_sup) {
          color = rango.color;
        }
      }
      return color == "" ? "white" : color;
    },

    singleClick(eventData: any): void {
      //Si es un gráfico de comparación por dimensión, no permitir agregar filtro al dar clic al gráfico
      if (this.indicador.configuracion.dimensionComparacion == "") {
        if (
          ["PIECHART", "PIE", "PASTEL", "TORTA"].includes(
            this.indicador.configuracion.tipo_grafico.toUpperCase()
          )
        ) {
          this.$emit("click-plot", eventData.points[0].label);
        } else {
          this.$emit("click-plot", eventData.points[0].x);
        }
      }
    },

    click(eventData: any): void {
      // Click fires once on single and twice on double clicks
      // We only care about single clicks.
      //This checks to give the doubleclick event 500 ms to fire, and does nothing if so
      const t0 = Date.now();
      const _this = this;
      if (t0 - this.doubleClickTime > this.doubleClickThreshold) {
        setTimeout(function() {
          if (t0 - _this.doubleClickTime > _this.doubleClickThreshold) {
            _this.singleClick(eventData);
          }
        }, this.doubleClickThreshold);
      }
    },

    doubleclick(): void {
      console.log("doble clic");
      this.doubleClickTime = Date.now();
      this.$emit("doubleclick");
    },

    selected(eventData: any) {
      if (eventData != undefined) {
        this.$emit("filtar-posicion", eventData.points);
      }
    },

    // Triggered when you double-click to turn off the lasso or box selection
    deselect(eventData: any): void {
      this.doubleClickTime = Date.now();
      this.$emit("quitar-filtros", eventData);
    },

    aplicarOrden(data: any): object[] {
      let datos_ = data;
      //Aplicar otros filtros
      if (this.indicador.otros_filtros.elementos.length > 0) {
        const filtros_ = this.indicador.otros_filtros.elementos;
        datos_ = datos_.filter((d: any) => {
          return filtros_.includes(d.x);
        });
      }

      //Verificar primero si tiene orden X

      if (this.indicador.configuracion.orden_x != "") {
        datos_ =
          this.indicador.configuracion.orden_x == "asc"
            ? datos_.sort((a: any, b: any) =>
                isNaN(a.x) || isNaN(b.x) ? a.x.localeCompare(b.x) : a.x - b.x
              )
            : datos_.sort((a: any, b: any) =>
                isNaN(a.x) || isNaN(b.x) ? b.x.localeCompare(a.x) : b.x - a.x
              );
      } else if (this.indicador.configuracion.orden_y != "") {
        datos_ =
          this.indicador.configuracion.orden_y == "asc"
            ? datos_.sort((a: any, b: any) => a.y - b.y)
            : datos_.sort((a: any, b: any) => b.y - a.y);
      }

      return datos_;
    }
  }
});
