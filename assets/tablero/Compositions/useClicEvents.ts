import { ref } from "@vue/composition-api";

export default function(indicador: any, ctx: any) {
  const doubleClickTime = ref(0);
  const doubleClickThreshold = ref(500);
  const seleccionActiva = ref(false);

  const singleClick = (eventData: any) => {
    //Si es un gráfico de comparación por dimensión, no permitir agregar filtro al dar clic al gráfico
    if (indicador.configuracion.dimensionComparacion == "") {
      if (
        ["PIECHART", "PIE", "PASTEL", "TORTA"].includes(
          indicador.configuracion.tipo_grafico.toUpperCase()
        )
      ) {
        ctx.emit("click-plot", eventData.points[0].label);
      } else {
        ctx.emit("click-plot", eventData.points[0].x);
      }
    }
  };

  const click = (eventData: any) => {
    // Click fires once on single and twice on double clicks
    // We only care about single clicks.
    //This checks to give the doubleclick event 500 ms to fire, and does nothing if so
    const t0 = Date.now();
    if (t0 - doubleClickTime.value > doubleClickThreshold.value) {
      setTimeout(function() {
        if (t0 - doubleClickTime.value > doubleClickThreshold.value) {
          singleClick(eventData);
        }
      }, doubleClickThreshold.value);
    }
  };

  const doubleclick = (): void => {
    console.log("doble clic");
    doubleClickTime.value = Date.now();
    ctx.$emit("doubleclick");
  };

  const selected = (eventData: any): void => {
    if (eventData != undefined) {
      ctx.$emit("filtar-posicion", eventData.points);
    }
  };

  // Triggered when you double-click to turn off the lasso or box selection
  const deselect = (eventData: any): void => {
    doubleClickTime.value = Date.now();
    ctx.$emit("quitar-filtros", eventData);
  };

  return {
    doubleClickTime,
    doubleClickThreshold,
    seleccionActiva,
    deselect,
    selected,
    doubleclick,
    click,
    singleClick
  };
}
