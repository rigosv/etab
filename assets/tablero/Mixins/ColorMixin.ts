import { defineComponent } from "@vue/composition-api";

export default defineComponent ({
  methods : {
    getColorExceljs(codigo: string): string {
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
  }
})
