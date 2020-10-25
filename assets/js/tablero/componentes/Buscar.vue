<template>
      <div class="col-auto">
          <div class="input-group mb-2">
              <div class="input-group-prepend">
                  <div class="input-group-text">
                      <font-awesome-icon icon="search" v-if="!cc_salas" />
                      <strong class="text-danger"><i class="fas fa-sync fa-spin" v-if="cc_salas"></i></strong>
                  </div>
              </div>

              <input
                  autocomplete="off"
                  ref="input"
                  type="text"
                  class="form-control"
                  :placeholder=" (enter) ? $t('_presione_enter_iniciar_busqueda_') : $t('_buscar_')"
                  :value="value"
                  @input="$emit('input', $event.target.value)"
                  @keyup.enter="$emit('buscar', $event.target.value)"
              />
          </div>
      </div>
</template>

<script>
export default {
    props: {
        value: String,
        enter: {type: Boolean, default: false}
    },
    data : function () {
        return {
            cc_salas : false,
        }
    },
    methods : {
        focus: function () {
            this.$refs.input.focus();
        }
    }
}
</script>