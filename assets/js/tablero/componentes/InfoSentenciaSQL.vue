<template>
    <div style="max-height:400px; max-width:100%; overflow:auto;">
        <pre>
            <highlight-code >
                {{ indicador.sql }}
            </highlight-code>
        </pre>
    </div>
</template>

<script>
    import axios from 'axios';
    import sqlFormatter from "sql-formatter";


    export default {
        props: {
              indicador: Object
        },
        mounted : function (){
            let json = { filtros: "", ver_sql: true, tendencia: false };

            this.indicador.cargando = true;
            let self = this;
            axios.post( "/api/v1/tablero/datosIndicador/" + this.indicador.id + '/' + this.indicador.dimension, json )
                .then(function (response) {
                    if (response.status == 200) {
                        self.indicador.sql = sqlFormatter.format(response.data.data);
                    }
                    self.indicador.cargando = false;
                })
                .catch(function (error) {
                    console.log(error);
                    self.indicador.cargando = false;
                });              
        }

    }
</script>