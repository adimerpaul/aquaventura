<template>
<q-page>
  <div class="row">
    <div class="col-2"></div>
    <div class="col-8">
      <q-form @submit.prevent="cardSearch">
        <q-input v-model="card" label="Card" type="text" required outlined autofocus clearable />
      </q-form>
    </div>
    <div class="col-2"></div>
  </div>
</q-page>
</template>
<script>
export default {
  name: 'CardsItem',
  data () {
    return {
      card: ''
    }
  },
  methods: {
    cardSearch () {
      this.$api.get('cards/' + this.card).then((response) => {
        if (response.data) {
          console.log(response.data)
        } else {
          this.$q.notify({
            message: 'Targeta no registrada',
            color: 'negative',
            position: 'top',
            timeout: 2000
          })
        }
      })
    }
  }
}
</script>

<style scoped>

</style>
