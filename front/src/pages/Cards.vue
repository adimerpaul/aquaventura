<template>
<q-page>
  <div class="row q-pa-xs">
    <div class="col-2"></div>
    <div class="col-8">
      <q-form @submit.prevent="cardSearch">
        <q-input v-model="target" :loading="loading" ref="card" label="Card" type="text" required outlined autofocus clearable>
          <template v-slot:append>
            <q-icon name="search" @click="$refs.card.focus()" round flat color="primary" :disable="loading" />
          </template>
        </q-input>
      </q-form>
    </div>
    <div class="col-2"></div>
    <div class="col-3"></div>
    <div class="col-6">
      <q-card bordered class="q-mt-xs">
        <q-card-section class="q-pb-none">
          <div class="text-h6">Credencial</div>
        </q-card-section>
        <q-card-section class="q-pt-none">
          <div class="row">
            <div class="col-2">
              <q-img v-if="card.photo!=undefined" :src="`${url}../images/${card.photo}`" />
            </div>
            <div class="col-10 q-pl-xs">
              <div class="text-subtitle2"><b>Nombre: </b>{{ card.name }}</div>
              <div class="text-subtitle2"><b>CI: </b>{{ card.ci }}</div>
              <div class="text-subtitle2"><b>Fecha de Nacimiento: </b>{{ card.birthday }}</div>
              <div class="text-subtitle2"><b>Fecha de Inicio: </b>{{ card.dateIni }}</div>
              <div class="text-subtitle2"><b>Fecha de Vencimiento: </b>{{ card.dateEnd }}</div>
            </div>
          </div>
        </q-card-section>
      </q-card>
    </div>
    <div class="col-3"></div>
  </div>
</q-page>
</template>
<script>
export default {
  name: 'CardsItem',
  data () {
    return {
      url: process.env.API,
      target: '',
      card: {},
      loading: false
    }
  },
  methods: {
    cardSearch () {
      this.card = {}
      this.loading = true
      this.$api.get('cards/' + this.target).then((response) => {
        if (response.data) {
          this.card = response.data
        } else {
          this.$q.notify({
            message: 'Targeta no registrada',
            color: 'negative',
            position: 'top',
            timeout: 2000
          })
        }
      }).catch((error) => {
        this.$q.notify({
          message: error.response.data.message,
          color: 'negative',
          position: 'top',
          timeout: 2000
        })
      }).finally(() => {
        this.loading = false
        this.target = ''
        this.$refs.card.focus()
      })
    }
  }
}
</script>

<style scoped>

</style>
