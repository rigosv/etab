<template>
    <div class="container-fluid row" style="padding: 20px 0px 0px 0px; ">

        <b-card no-body class="col-12">
            <b-tabs pills card>
                <b-tab active>
                    <template slot="title">
                        <font-awesome-icon icon='clipboard-list' /> {{ $t('_ficha_tecnica_') }}
                    </template>
                    <b-card-text>
                        <InfoFichaTecnica :indicador="indicador" />
                    </b-card-text>
                </b-tab>
                <b-tab>
                    <template slot="title">
                        <font-awesome-icon icon="table" /> {{ $t('_tabla_datos_') }}
                    </template>
                    <b-card-text>
                        <InfoTablaDatos :indicador="indicador" />
                    </b-card-text>
                </b-tab>
                <b-tab v-if="indicador.informacion.rangos.length > 0">
                    <template slot="title">
                        <font-awesome-icon icon='bell' /> {{ $t('_rangos_alerta_') }}
                    </template>
                    <b-card-text>
                        <InfoRangosAlerta :indicador="indicador" />
                    </b-card-text>
                </b-tab>
                <b-tab >
                    <template slot="title">
                        <font-awesome-icon icon="bolt" /> {{ $t('_sentencia_sql_') }}
                    </template>
                    <b-card-text>
                        <InfoSentenciaSQL :indicador="indicador" />
                    </b-card-text>
                </b-tab>
            </b-tabs>
        </b-card>
    </div>
</template>

<script>    
    import InfoFichaTecnica from "./InfoFichaTecnica";
    import InfoTablaDatos from "./InfoTablaDatos";
    import InfoSentenciaSQL from './InfoSentenciaSQL';
    import InfoRangosAlerta from './InfoRangosAlerta';

    export default {
        components : {InfoFichaTecnica, InfoTablaDatos, InfoSentenciaSQL, InfoRangosAlerta},
        props: {
            indicador: Object,
            index: Number
        }
    }
</script>