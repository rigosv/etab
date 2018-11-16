window.SONATA_CONFIG = {
    USE_ICHECK: true,
    USE_SELECT2: true
};
$(document).ready(function() {
    // id que se está usando para los nombres de los formularios
    let $campos = $('textarea[id$="_camposIndicador"]');
    
    $campos.hide();

    let campos_list = $campos.html().split(',');

    let listado = '<UL id="campos_orden">';
    $.each(campos_list, function(i, nodo) {
        listado += '<ol class="ui-state-default" data="' + nodo + '"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>' + nodo + '</ol>';
    });
    listado += '</UL>';
    $campos.parent().append(listado);

    $("#campos_orden").sortable({
        stop: function(event, ui) {
            cambiar_orden();
        }
    });

    //Al seleccionar una variable pasarla al campo de la fórmula
    $('select[id$="_variables"]').on("select2-selecting", function(e) { 
        let variable = $.trim(e.choice.text.match(/(\([0-9A-Z_]+\)$)/g));
        variable = variable.replace('(','{').replace(')','}');
        $("input[id$='_formula']").atCaret('insert', variable);
    }).on("select2-removed", function(e) { 
        let variable = $.trim(e.choice.text.match(/(\([0-9A-Z_]+\)$)/g));
        variable = variable.replace('(','{').replace(')','}');
        $("input[id$='_formula']").val($("input[id$='_formula']").val().replace(variable, ''));
    });

    //Cambiar la estructura de la clasificación técnica para agruparlos por categorías
    let categoria = '', cat, catSlug;

    //Elimino la etiqueta
    $('div[id$="_clasificacionTecnica"]').find('label:first').remove();

    $('ul[id$="_clasificacionTecnica"] li').each(function(i, nodo) {
        //Viene con el formato categoria -- nombre clasificación
        cat = $(nodo).find('span').html().split('--'); 
        
        catSlug = slugify(cat[0]);
        if (  catSlug != categoria ){                    
            categoria = catSlug;
            //Agregar la categoría como un elemento independiente
            $('ul[id$="_clasificacionTecnica"]').append($('<LI id="id_'+catSlug+'"><B>'+cat[0]+'</B><ul class="clasificaciones-tecnicas"></ul></LI><BR/>'));
        }
        //Cambiar la etiqueta, dejar solo el nombre de la categoria
        $(nodo).find('span').html(cat[1]);
        $(nodo).find('input').attr('data-categoria', catSlug).addClass(catSlug);
        
        //Agregarlo al nodo de la categoía
        $(nodo).appendTo('#id_'+catSlug+' ul');
    });
    
    //Verificar que cuando se elija la clasificación técnica
    // solo se pueda marcar uno por categoría
    $('ul[id$="_clasificacionTecnica"] input').on('ifChecked', function(event){
        
        let id = $(this).attr('id');
        let categoria = $(this).data('categoria');
        
        //Desmarcar las otras clasificaciones dentro de la misma categoría
        $('input[id!='+id+'].'+categoria).iCheck('uncheck');
        
    });
    
    function cambiar_orden() {
        let campos_nvo_orden = '';
        $("#campos_orden").children().each(function(i, nodo) {
            campos_nvo_orden += $(nodo).attr('data') + ',';
        });

        $campos.html(campos_nvo_orden.substring(0, campos_nvo_orden.length - 1));
    }
    
    alertas_aplicar_estilos();
    $('div[id$=_alertas]').on('sonata.add_element', function(event) {
        alertas_aplicar_estilos();
    });
    
    function alertas_aplicar_estilos(){
        $('div[id$=_alertas] select[id$=_color]').css('width','150px');
        $('div[id$=_alertas] label').remove();
        $('div[id$=_alertas] textarea[id$=_comentario]').css('width','350px');
    }
});

function slugify(text)
{
  return text.toString().toLowerCase()
    .replace(/\s+/g, '-')           // Replace spaces with -
    .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
    .replace(/\-\-+/g, '-')         // Replace multiple - with single -
    .replace(/^-+/, '')             // Trim - from start of text
    .replace(/-+$/, '');            // Trim - from end of text
}
