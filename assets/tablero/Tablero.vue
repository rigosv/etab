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
import { defineComponent, onMounted } from "@vue/composition-api";

import MenuTablero from "./componentes/MenuTablero.vue";
import Sala from "./componentes/Sala.vue";

export default defineComponent({
  components: {
    MenuTablero,
    Sala
  },
  props: {
    idSala: String,
    token: String,
    lang: String
  },
  setup(props) {
    onMounted(() => {
      this.$store.commit("addDatosSalaPublica", {
        idSala: props.idSala,
        token: props.token
      });
      //loadLanguageAsync(this.lang);
      this.$i18n.locale = props.lang;
    });

    const convertirGraficosSala = () => {
      //Extraer cada gráfico y convertirlo en formato png para que sea más
      // fácil de convertir a pdf
      this.$store.state.indicadores.map((indicador: any) => {
        const img = document.querySelector("#graph-export-" + indicador.index);
        this.$refs.sala.$refs["indicador" + indicador.index][0]
          .graficoImagen({ format: "png", height: 500, width: 685 })
          .then((dataUrl: string) => {
            img?.setAttribute("src", dataUrl);
          });
      });
    };

    return {
      convertirGraficosSala
    };
  }
});
</script>
