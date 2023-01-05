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
    <div class="col-12" v-for="q in queries" :key="q.id">
      <q-card class="q-mt-none">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle2">{{ q.date }} {{ q.name }} <span class="text-caption">{{ q.type }} {{ q.days }} {{ q.schedule }}</span></div>
          <q-space />
          {{ q.codeTarget }}
        </q-card-section>
        <q-card-section class="q-pt-none">
          <template  v-for="(r,i) in q.records" :key="`${i}C${r.id}`">
            <q-chip text-color="white" :color="r.record?'green':'red'" dense>
              {{ r.datedmY }}
              <q-badge rounded floating>{{r.day.substr(0,1)}}</q-badge>
            </q-chip>
          </template>
        </q-card-section>
      </q-card>
    </div>
<!--    <pre>{{queries}}</pre>-->
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
      schedule: '06:00-07:00',
      schedules: this.$schedules,
      queries: []
    }
  },
  mounted () {
    this.queriesGet()
  },
  methods: {
    queriesGet () {
      this.loading = true
      this.$api.post('queries', {
        dateIni: this.dateWeekIni,
        dateEnd: this.dateWeekEnd,
        schedule: this.schedule
      }).then((response) => {
        this.loading = false
        this.queries = response.data
        this.$q.notify({
          message: 'Consulta realizada',
          color: 'positive',
          icon: 'check',
          position: 'top'
        })
      }).catch((error) => {
        this.loading = false
        this.$q.notify({
          message: error.response.data.message,
          color: 'negative',
          icon: 'close',
          position: 'top'
        })
      })
    }
  }
}
</script>

<style scoped>

</style>
