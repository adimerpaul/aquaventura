<template>
  <q-page class="q-pa-xs">
    <div class="row">
      <div class="col-12 col-md-4">
        <q-input dense v-model="dateIni" outlined label="Fecha Inicio" type="date" />
      </div>
      <div class="col-12 col-md-4">
        <q-input dense v-model="dateEnd" outlined label="Fecha Fin" type="date" />
      </div>
      <div class="col-12 col-md-4 flex flex-center">
        <q-btn v-if="store.permissions.includes('Consultar registos')" color="primary" label="Consultar" icon="search" @click="recordsGet" />
      </div>
      <div class="col-12">
        <q-table title="Historial de registro" :rows="records" dense flat :rows-per-page-options="[20,50,100,0]" :loading="loading" :columns="recordColumns" :filter="filter">
          <template v-slot:top-right>
            <div class="row">
              <div class="col-2 flex flex-center">
                <q-btn @click="download" v-if="store.permissions.includes('Descargar registros')" icon="cloud_download" flat no-caps dense :loading="loading" />
              </div>
              <div class="col-2 flex flex-center">
                <q-btn @click="recordsGet" icon="refresh" flat no-caps dense :loading="loading" />
              </div>
              <div class="col-8 flex flex-center">
                <q-input dense outlined v-model="filter" label="Buscar" clearable>
                  <template v-slot:append>
                    <q-icon name="search" />
                  </template>
                </q-input>
              </div>
            </div>
          </template>
        </q-table>
      </div>
    </div>
  </q-page>
</template>

<script>
import moment from 'moment'
import { jsPDF } from 'jspdf'
import xlsx from 'json-as-xlsx'
import { useCounterStore } from 'stores/example-store'

export default {
  name: 'historyItem',
  data () {
    return {
      store: useCounterStore(),
      dateIni: moment().format('YYYY-MM-DD'),
      dateEnd: moment().format('YYYY-MM-DD'),
      url: process.env.API,
      PDF: jsPDF,
      recordColumns: [
        { name: 'name', label: 'Nombre', field: 'name', align: 'left', sortable: true },
        { name: 'date', label: 'Fecha', field: 'date', align: 'left', sortable: true },
        { name: 'time', label: 'Hora', field: 'time', align: 'left', sortable: true },
        { name: 'code', label: 'Código', field: 'code', align: 'left', sortable: true },
        { name: 'type', label: 'Tipo', field: 'type', align: 'left', sortable: true },
        { name: 'observation', label: 'Observación', field: 'observation', align: 'left', sortable: true }
      ],
      recordDialog: false,
      recordDialogPhoto: false,
      recordCreate: false,
      filter: '',
      records: [],
      record: {},
      loading: false
    }
  },
  created () {
    this.recordsGet()
  },
  methods: {
    download () {
      const data = [
        {
          columns: [
            { label: 'id', value: 'id' },
            { label: 'ci', value: 'ci' },
            { label: 'code', value: 'code' },
            { label: 'dateIni', value: 'dateIni' },
            { label: 'dateEnd', value: 'dateEnd' },
            { label: 'codeTarget', value: 'codeTarget' },
            { label: 'name', value: 'name' },
            { label: 'birthday', value: 'birthday' },
            { label: 'phone', value: 'phone' },
            { label: 'schedule', value: 'schedule' },
            { label: 'amount', value: 'amount' },
            { label: 'type', value: 'type' },
            { label: 'observation', value: 'observation' },
            { label: 'date', value: 'date' },
            { label: 'time', value: 'time' }
          ],
          content: this.records
        }
      ]
      const settings = {
        fileName: moment().format('YYYY-MM-DD HH:mm:ss')
      }
      xlsx(data, settings) // Will download the excel file
    },
    finishFn (file) {
      this.recordsGet()
      this.recordDialogPhoto = false
    },
    recordsCreate () {
      if (this.record.schedule === '') {
        this.$q.notify({
          message: 'Debe seleccionar un horario',
          color: 'negative',
          icon: 'warning',
          position: 'top'
        })
        return
      }
      this.loading = true
      if (this.recordCreate) {
        this.$api.post('/records', this.record)
          .then(response => {
            this.records.unshift(response.data)
            this.recordDialog = false
            this.loading = false
          })
          .catch(error => {
            this.loading = false
            this.$q.notify({
              message: error.response.data.message,
              color: 'negative',
              icon: 'error',
              position: 'top'
            })
          })
      } else {
        this.$api.put('/records/' + this.record.id, this.record)
          .then(response => {
            this.records.splice(this.records.findIndex(item => item.id === this.record.id), 1, response.data)
            this.recordDialog = false
            this.loading = false
          })
          .catch(error => {
            this.loading = false
            this.$q.notify({
              message: error.response.data.message,
              color: 'negative',
              icon: 'error',
              position: 'top'
            })
          })
      }
    },
    recordsEdit (record) {
      this.record = record
      this.recordDialog = true
      this.recordCreate = false
    },
    recordsCard (record) {
      this.loading = true
      this.$api.get('base64/' + record.photo)
        .then(response => {
          const doc = new this.PDF()
          const img = new Image()
          img.src = 'credential.png'
          doc.addImage(img, 'PNG', 3, 3, 80, 50)
          doc.addImage(response.data, 'JPEG', 5, 14, 28, 28)
          doc.setFontSize(8)
          doc.setFont('helvetica', 'bold')
          doc.text(record.name, 37, 18, { maxWidth: 35, align: 'left' })
          doc.setFontSize(6)
          doc.setFont('helvetica', 'normal')
          doc.text(record.phone, 40, 33)
          doc.text(record.schedule, 40, 37)
          doc.text(record.type, 40, 41)
          doc.save(moment().format('YYYY-MM-DD HH:MM:SS') + '.pdf')
        })
        .catch(error => {
          this.loading = false
          this.$q.notify({
            message: error.response.data.message,
            color: 'negative',
            icon: 'error',
            position: 'top'
          })
        })
        .finally(() => {
          this.loading = false
        })
    },
    recordsPhoto (record) {
      this.record = record
      this.recordDialogPhoto = true
    },
    recordsDelete (record) {
      this.$q.dialog({
        title: 'Eliminar tarjeta',
        message: '¿Está seguro de eliminar la tarjeta?',
        ok: 'Eliminar',
        cancel: 'Cancelar',
        persistent: true
      }).onOk(() => {
        this.loading = true
        this.$api.delete('/records/' + record.id)
          .then(response => {
            this.records.splice(this.records.indexOf(record), 1)
            this.$q.notify({
              message: 'Tarjeta eliminada',
              color: 'positive',
              icon: 'check_circle',
              position: 'top'
            })
          })
          .catch(error => {
            this.$q.notify({
              message: error.response.data.message,
              color: 'negative',
              icon: 'error',
              position: 'top'
            })
          }).finally(() => {
            this.loading = false
          })
      })
    },
    recordsAdd () {
      this.recordCreate = true
      this.recordDialog = true
      this.record = {
        ci: '',
        code: '',
        dateIni: moment().format('YYYY-MM-DD'),
        dateEnd: moment().format('YYYY-MM-DD'),
        codeTarget: '',
        name: '',
        birthday: moment().format('YYYY-MM-DD'),
        phone: '',
        schedule: '',
        amount: '',
        type: 'REGULARES',
        observation: ''
      }
    },
    recordsGet () {
      this.loading = true
      this.$api.post('history', {
        dateIni: this.dateIni,
        dateEnd: this.dateEnd
      }).then((response) => {
        this.records = response.data
      }).catch((error) => {
        this.$q.notify({
          message: error.response.data.message,
          color: 'negative',
          position: 'top',
          timeout: 2000
        })
      }).finally(() => {
        this.loading = false
      })
    }
  }
}
</script>

<style scoped>

</style>
