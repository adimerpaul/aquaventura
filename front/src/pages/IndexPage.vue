<template>
  <q-page>
    <div class="row">
      <div class="col-6 col-md-4">
        <q-input dense v-model="dateIni" outlined label="Fecha Inicio" type="date" />
      </div>
      <div class="col-6 col-md-4">
        <q-input dense v-model="dateEnd" outlined label="Fecha Fin" type="date" />
      </div>
      <div class="col-12 col-md-4 flex flex-center">
        <q-btn color="primary" :loading="loading" label="Consultar" icon="search" v-if="store.permissions.includes('Consultar principal')" @click="query" />
      </div>
      <div class="col-12 col-md-6">
        <div class="text-center text-subtitle2">Asistencias</div>
        <apexcharts type="bar" :options="chartOptions" :series="series"></apexcharts>
      </div>
      <div class="col-12 col-md-6">
        <div class="text-center text-subtitle2">Horarios</div>
        <apexcharts width="380" type="donut" :options="options" :series="series1"></apexcharts>
      </div>
    </div>
  </q-page>
</template>
<script>
import VueApexCharts from 'vue3-apexcharts'
import moment from 'moment'
import { useCounterStore } from 'stores/example-store'
export default {
  name: 'IndexPage',
  data () {
    return {
      store: useCounterStore(),
      dateIni: moment().isoWeek() === 1 ? moment().subtract(1, 'week').format('YYYY-MM-DD') : moment().startOf('isoWeek').format('YYYY-MM-DD'),
      dateEnd: moment().isoWeek() === 1 ? moment().subtract(1, 'week').format('YYYY-MM-DD') : moment().endOf('isoWeek').format('YYYY-MM-DD'),
      loading: false,
      options: {
        chart: {
          type: 'donut'
        },
        labels: []
      },
      series1: [],
      chartOptions: {},
      series: [{
        name: 'series-1',
        data: []
      }]
    }
  },
  components: {
    apexcharts: VueApexCharts
  },
  created () {
    if (!this.store.isLoggedIn) {
      this.$router.push('/login')
    }
  },
  mounted () {
    this.query()
  },
  methods: {
    query () {
      this.loading = true
      this.$api.post('query', {
        dateIni: this.dateIni,
        dateEnd: this.dateEnd
      }).then(res => {
        const categories = []
        const series = []
        const labels = []
        const series1 = []
        res.data.asistencias.forEach(item => {
          categories.push(item.date)
          series.push(item.cantidad)
        })
        res.data.schedule.forEach(item => {
          labels.push(item.schedule)
          series1.push(item.cantidad)
        })
        this.chartOptions = {
          chart: {
            id: 'basic-bar'
          },
          xaxis: {
            categories
          }
        }
        this.series = [{
          name: 'series-1',
          data: series
        }]
        this.options = {
          chart: {
            type: 'donut'
          },
          labels
        }
        this.series1 = series1
      }).catch(err => {
        console.log(err)
      }).finally(() => {
        this.loading = false
      })
    }
  }
}
</script>
