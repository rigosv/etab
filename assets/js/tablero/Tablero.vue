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

<script>
    
    import MenuTablero from './componentes/MenuTablero';
    import Sala from './componentes/Sala';
    import alasql from 'alasql';

    export default  {
        components: { MenuTablero, Sala},
        props: {
            idSala: {
                type: String,
                default: ''
            },
            token: {
                type: String,
                default: ''
            }
        },
        mounted : function() {
            this.$store.commit('addDatosSalaPublica', {idSala:this.idSala, token:this.token});
        }, 
        methods : {
            convertirGraficosSala :  function () {
                //Extraer cada gráfico y convertirlo en formato png para que sea más
                // fácil de convertir a pdf
                this.$store.state.indicadores.map( indicador => {
                    let img = document.querySelector('#graph-export-'+indicador.index);
                    this.$refs.sala.$refs['indicador'+indicador.index][0].graficoImagen({format: 'png', height: 500,width: 685}).then( dataUrl => {
                        img.setAttribute("src", dataUrl);
                    })                     
                });
            }
        }
    }
</script>