<script>
    export default {
        methods: {
            getColorExceljs ( codigo ){
                if ( ['green'].includes(codigo) ){
                    return 'FF008000';
                } else if ( ['red'].includes(codigo) ){
                    return 'FFFF0000';
                } else if ( ['orange', '#FF8000'].includes(codigo)){
                    return 'FFFAA500';
                } else if ( ['yellow', '#FFFF66'].includes(codigo) ){
                    return 'FFFFFF00';
                }
            }            
        }
    }
</script>