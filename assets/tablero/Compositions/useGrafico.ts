import { computed } from "@vue/composition-api";

export default function(indicador: any) {
  const dec = computed((): number => {
    return isNaN(indicador.ficha.cantidad_decimales) ||
      indicador.ficha.cantidad_decimales == null
      ? 2
      : indicador.ficha.cantidad_decimales;
  });

  const nombreDimension = computed((): string => {
    return indicador.dimension != undefined &&
      indicador.informacion.dimensiones != undefined
      ? indicador.informacion.dimensiones[
          indicador.dimension
        ].descripcion.toUpperCase()
      : "";
  });

  const aplicarOrden = (data: any) => {
    let datos_ = data;
    //Aplicar otros filtros
    if (indicador.otros_filtros.elementos.length > 0) {
      const filtros_ = indicador.otros_filtros.elementos;
      datos_ = datos_.filter((d: any) => {
        return filtros_.includes(d.x);
      });
    }

    //Verificar primero si tiene orden X

    if (indicador.configuracion.orden_x != "") {
      datos_ =
        indicador.configuracion.orden_x == "asc"
          ? datos_.sort((a: any, b: any) =>
              isNaN(a.x) || isNaN(b.x) ? a.x.localeCompare(b.x) : a.x - b.x
            )
          : datos_.sort((a: any, b: any) =>
              isNaN(a.x) || isNaN(b.x) ? b.x.localeCompare(a.x) : b.x - a.x
            );
    } else if (indicador.configuracion.orden_y != "") {
      datos_ =
        indicador.configuracion.orden_y == "asc"
          ? datos_.sort((a: any, b: any) => a.y - b.y)
          : datos_.sort((a: any, b: any) => b.y - a.y);
    }

    return datos_;
  };

  const datosOrdenados = computed(() => {
    const datos_ = indicador.data.map((f: any) => {
      return { x: f.category, y: f.measure };
    });

    return aplicarOrden(datos_);
  });

  return {
    dec,
    nombreDimension,
    datosOrdenados,
    aplicarOrden
  };
}
