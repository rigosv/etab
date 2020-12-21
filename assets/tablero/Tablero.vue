<template>
  <div>
    <header>
      <MenuTablero @convertir-graficos-sala="convertirGraficosSala()" />
    </header>
    <main>
      <Sala ref="sala" />
    </main>
    <footer>
      <vue-snotify></vue-snotify>
    </footer>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "@vue/composition-api";

import MenuTablero from "./Componentes/MenuTablero.vue";
import Sala from "./Componentes/Sala.vue";

export default defineComponent({
  components: {
    MenuTablero,
    Sala
  },
  props: {
    idSala: String,
    token: String,
    lang: { default: "es", type: String }
  },

  mounted() {
    this.$store.commit("addDatosSalaPublica", {
      idSala: this.idSala,
      token: this.token
    });
    //loadLanguageAsync(this.lang);
    this.$i18n.locale = this.lang;
  },

  methods: {
    convertirGraficosSala() {
      //Extraer cada gráfico y convertirlo en formato png para que sea más
      // fácil de convertir a pdf
      this.$store.state.indicadores.map((indicador: any) => {
        const img = document.querySelector("#graph-export-" + indicador.index);
        ((this.$refs.sala as Vue).$refs["indicador" + indicador.index] as Array<
          any
        >)[0]
          .graficoImagen({ format: "png", height: 500, width: 685 })
          .then((dataUrl: string) => {
            img?.setAttribute("src", dataUrl);
          });
      });
    }
  }
});
</script>
