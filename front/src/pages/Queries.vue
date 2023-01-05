<template>
<q-page class="q-pa-xs">
  <div class="row">
    <div class="col-6 col-md-3">
      <q-input v-model="dateWeekIni" outlined dense label="Fecha inicial" type="date" />
    </div>
    <div class="col-6 col-md-3">
      <q-input v-model="dateWeekEnd" outlined dense label="Fecha final" type="date" />
    </div>
    <div class="col-6 col-md-3">
      <q-select v-model="schedule" outlined dense label="Horario" :options="schedules" />
    </div>
    <div class="col-6 col-md-3 flex flex-center">
      <q-btn color="primary" :loading="loading" label="Consultar" icon="search" @click="queriesGet" />
    </div>
  </div>
</q-page>
</template>

<script>
import { useCounterStore } from 'stores/example-store'
import moment from 'moment'

export default {
  name: 'QueriesItem',
  data () {
    return {
      loading: false,
      dateWeekIni: moment().startOf('week').add(1, 'days').format('YYYY-MM-DD'),
      dateWeekEnd: moment().endOf('week').add(1, 'days').format('YYYY-MM-DD'),
      store: useCounterStore(),
      schedule: '06:00 - 07:00',
      schedules: [
        '06:00 - 07:00',
        '07:00 - 08:00',
        '08:00 - 09:00',
        '09:00 - 10:00',
        '10:00 - 11:00',
        '11:00 - 12:00',
        '12:00 - 13:00',
        '13:00 - 14:00',
        '14:00 - 15:00',
        '15:00 - 16:00',
        '16:00 - 17:00',
        '17:00 - 18:00',
        '18:00 - 19:00',
        '19:00 - 20:00',
        '20:00 - 21:00',
        '21:00 - 22:00'
      ]
    }
  },
  methods: {
    queriesGet () {
      this.loading = true
      this.store.queriesGet(this.dateWeekIni, this.dateWeekEnd, this.schedule)
        .then(() => {
          this.loading = false
        })
        .catch(() => {
          this.loading = false
        })
    }
  }
}
</script>

<style scoped>

</style>
