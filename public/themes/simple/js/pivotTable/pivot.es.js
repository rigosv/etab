// Generated by CoffeeScript 1.7.1
(function() {
  var esFmt, esFmtInt, esFmtPct, nf, tpl;

  nf = $.pivotUtilities.numberFormat;

  tpl = $.pivotUtilities.aggregatorTemplates;

  esFmt = nf({
    thousandsSep: ",",
    decimalSep: "."
  });

  esFmtInt = nf({
    digitsAfterDecimal: 1,
    thousandsSep: ",",
    decimalSep: "."
  });

  esFmtPct = nf({
    digitsAfterDecimal: 1,
    scaler: 100,
    suffix: "%",
    thousandsSep: ",",
    decimalSep: "."
  });

  $.pivotUtilities.locales.es = {
    localeStrings: {
      renderError: "Ocurrió un error dibujando la tabla pivote.",
      computeError: "Ocurrió un error calculando la tabla pivote.",
      uiRenderError: "Ocurrió un error dibujando la interfaz de la tabla pivote.",
      selectAll: "Seleccionar todo",
      selectNone: "Deseleccionar todo",
      tooMany: "(demasiados para listarlos)",
      filterResults: "Filtrar resultados",
      totals: "Totales",
      vs: "vs",
      by: "por"
    },    
    aggregators: {
      "Contar": tpl.count(esFmtInt),
      "Contar valores únicos": tpl.countUnique(esFmtInt),
      "Listar valores únicos": tpl.listUnique(", "),
      "Suma": tpl.sum(esFmt),
      "Suma entera": tpl.sum(esFmtInt),
      "Promedio": tpl.average(esFmt),
      "Suma sobre suma": tpl.sumOverSum(esFmt),
      "80% Upper Bound": tpl.sumOverSumBound80(true, esFmt),
      "80% Lower Bound": tpl.sumOverSumBound80(false, esFmt),
      "Suma como fracción del Total": tpl.fractionOf(tpl.sum(), "total", esFmtPct),
      "Suma como fracción de filas": tpl.fractionOf(tpl.sum(), "row", esFmtPct),
      "Suma como fracción de columnas": tpl.fractionOf(tpl.sum(), "col", esFmtPct),
      "Contar sobre facción del Total": tpl.fractionOf(tpl.count(), "total", esFmtPct),
      "Contar sobre fracción de filas": tpl.fractionOf(tpl.count(), "row", esFmtPct),
      "Contar sobre fracción de columnas": tpl.fractionOf(tpl.count(), "col", esFmtPct)
    },
    renderers: {
      "Tabla": $.pivotUtilities.renderers["Table"],
      "Table avec barres": $.pivotUtilities.renderers["Table Barchart"],
      "Carte de chaleur": $.pivotUtilities.renderers["Heatmap"],
      "Carte de chaleur par ligne": $.pivotUtilities.renderers["Row Heatmap"],
      "Carte de chaleur par colonne": $.pivotUtilities.renderers["Col Heatmap"]
    }
  };

}).call(this);