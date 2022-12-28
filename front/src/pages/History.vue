<template>
  <q-page class="q-pa-xs">
    <q-table :rows="records" dense flat :rows-per-page-options="[20,50,100,0]" :loading="loading" :columns="recordColumns" :filter="filter">
      <template v-slot:body-cell-actions="props">
        <q-td :props="props" auto-width>
          <q-btn-dropdown label="Opciones" no-caps dense color="primary">
            <q-list>
              <q-item clickable v-close-popup @click="recordsEdit(props.row)">
                <q-item-section avatar>
                  <q-icon name="o_edit" />
                </q-item-section>
                <q-item-section>Modificar</q-item-section>
              </q-item>
              <q-item clickable v-close-popup @click="recordsDelete(props.row)">
                <q-item-section avatar>
                  <q-icon name="o_delete" />
                </q-item-section>
                <q-item-section>Eliminar</q-item-section>
              </q-item>
              <q-item clickable v-close-popup @click="recordsPhoto(props.row)">
                <q-item-section avatar>
                  <q-icon name="o_photo_camera" />
                </q-item-section>
                <q-item-section>Foto</q-item-section>
              </q-item>
              <q-item clickable v-close-popup @click="recordsCard(props.row)">
                <q-item-section avatar>
                  <q-icon name="credit_card" />
                </q-item-section>
                <q-item-section>Tarjeta</q-item-section>
              </q-item>
            </q-list>
          </q-btn-dropdown>
        </q-td>
      </template>
      <template v-slot:top-right>
        <div class="row">
<!--          <div class="col-4 flex flex-center">-->
<!--            <q-btn @click="recordsAdd" color="primary" icon="add_circle_outline" dense label="Registrar" no-caps :loading="loading" />-->
<!--          </div>-->
          <div class="col-2 flex flex-center">
            <q-btn @click="recordsGet" icon="refresh" flat no-caps dense :loading="loading" />
          </div>
          <div class="col-10 flex flex-center">
            <q-input dense outlined v-model="filter" label="Buscar" clearable>
              <template v-slot:append>
                <q-icon name="search" />
              </template>
            </q-input>
          </div>
        </div>
      </template>
    </q-table>
    <q-dialog v-model="recordDialog">
      <q-card style="width: 700px;max-width: 85vh">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-h6">{{ recordCreate?'Crear':'Modificar'}} tarjeta</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>
        <q-card-section class="q-pt-xs">
          <q-form @submit="recordsCreate" class="">
            <div class="row">
              <div class="col-12 col-md-4">
                <q-input dense outlined v-model="record.ci" label="CI/NIT" hint="" />
              </div>
              <div class="col-12 col-md-8">
                <q-input dense outlined v-model="record.name" label="Nombre Completo" hint="" required />
              </div>
              <div class="col-12 col-md-6">
                <q-input dense outlined v-model="record.dateIni" label="Fecha de inicio" hint="" type="date" />
              </div>
              <div class="col-12 col-md-6">
                <q-input dense outlined v-model="record.dateEnd" label="Fecha de fin" hint="" type="date" />
              </div>
              <div class="col-12 col-md-6">
                <q-input dense outlined v-model="record.codeTarget" label="Código de tarjeta" hint="" required />
              </div>
              <div class="col-12 col-md-6">
                <q-input dense outlined v-model="record.birthday" label="Fecha de nacimiento" hint="" type="date" />
              </div>
              <div class="col-12 col-md-4">
                <q-input dense outlined v-model="record.phone" label="Teléfono/Celular" hint="" required />
              </div>
              <div class="col-12 col-md-4">
                <q-select dense outlined v-model="record.schedule" label="Horario" hint="" :options="schedules" required />
              </div>
              <div class="col-12 col-md-4">
                <q-select dense outlined v-model="record.type" label="Tipo" hint="" :options="['REGULARES','VACACIONES']"/>
              </div>
              <div class="col-12 col-md-6">
                <q-input dense outlined v-model="record.observation" label="Observación" hint="" type="textarea" />
              </div>
              <div class="col-12 col-md-12">
                <q-input dense outlined v-model="record.code" label="Código" hint="" clearable required/>
              </div>
            </div>
            <div class="row justify-end">
              <q-btn label="Cancelar" :loading="loading" color="red" icon="o_delete" no-caps v-close-popup />
              <q-btn :label="recordCreate?'Crear':'Modificar'" no-caps :loading="loading" :color="recordCreate?'green':'orange'" icon="o_check_circle" type="submit" />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>
    <q-dialog v-model="recordDialogPhoto">
      <q-card style="width: 350px;max-width: 85vh">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-h6">Subir foto</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>
        <q-card-section class="q-pt-xs">
          <q-form @submit="recordsCreate" class="">
            <div class="row">
              <div class="col-12 col-md-12 flex flex-center">
                <q-uploader
                  accept=".jpg, .png"
                  multiple
                  auto-upload
                  @finish="finishFn"
                  ref="uploader"
                  max-files="1"
                  auto-expand
                  :url="`${url}upload/${record.id}`"
                  stack-label="upload image"/>
              </div>
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import moment from 'moment'
import { jsPDF } from 'jspdf'
export default {
  name: 'historyItem',
  data () {
    return {
      url: process.env.API,
      PDF: jsPDF,
      schedules: [
        '06:00 - 07:00',
        '07:00 - 08:00',
        '08:00 - 09:00',
        '09:00 - 10:00',
        '10:00 - 11:00',
        '11:00 - 12:00',
        '12:00 - 13:00'
      ],
      recordColumns: [
        // { name: 'actions', label: 'Acciones', field: 'actions', align: 'left', sortable: true },
        { name: 'name', label: 'Nombre', field: 'name', align: 'left', sortable: true },
        { name: 'date', label: 'Fecha', field: 'date', align: 'left', sortable: true },
        { name: 'time', label: 'Hora', field: 'time', align: 'left', sortable: true },
        { name: 'code', label: 'Código', field: 'code', align: 'left', sortable: true },
        { name: 'type', label: 'Tipo', field: 'type', align: 'left', sortable: true },
        { name: 'observation', label: 'Observación', field: 'observation', align: 'left', sortable: true }
        // { name: 'dateIni', label: 'Fecha de inicio', field: 'dateIni', align: 'left', sortable: true },
        // { name: 'dateEnd', label: 'Fecha de fin', field: 'dateEnd', align: 'left', sortable: true },
        // { name: 'code', label: 'Código', field: 'code', align: 'left', sortable: true },
        // { name: 'codeTarget', label: 'Código de tarjeta', field: 'codeTarget', align: 'left', sortable: true }
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
      this.$api.get('records').then((response) => {
        console.log(response.data)
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
