import { ref } from "@vue/composition-api";

export default function() {
  const colores = ref([
    "rgb(31, 119, 180)",
    "rgb(174, 199, 232)",
    "rgb(255, 127, 14)",
    "rgb(163, 214, 163)",
    "rgb(214, 39, 40)",
    "rgb(255, 171, 169)",
    "rgb(148, 103, 189)",
    "rgb(197, 176, 213)",
    "rgb(140, 86, 75)",
    "rgb(196, 156, 148)",
    "rgb(227, 119, 194)",
    "rgb(247, 182, 210)",
    "rgb(255, 233, 211)",
    "rgb(152, 223, 138)"
  ]);
  function getColorExceljs(codigo: string): string {
    let resp = "";

    if (["green"].includes(codigo)) {
      resp = "FF008000";
    } else if (["red"].includes(codigo)) {
      resp = "FFFF0000";
    } else if (["orange", "#FF8000"].includes(codigo)) {
      resp = "FFFAA500";
    } else if (["yellow", "#FFFF66"].includes(codigo)) {
      resp = "FFFFFF00";
    }

    return resp;
  }

  const getColor = (valor: number, rangos: any): string => {
    let color = "";
    for (const rango of rangos) {
      if (valor >= rango.limite_inf && valor <= rango.limite_sup) {
        color = rango.color;
      }
    }
    return color == "" ? "white" : color;
  };

  const getColores = (datos_: any, rangos: any) => {
    let indice = 0;
    const colores_ = colores.value;

    return datos_.map((f: any) => {
      let color = getColor(f.measure, rangos);

      if (color == "white") {
        color = colores_[indice];
        indice = indice == colores_.length - 1 ? 0 : indice + 1;
      }

      return { x: f.category, color: color };
    });
  };

  return { colores, getColorExceljs, getColor, getColores };
}
