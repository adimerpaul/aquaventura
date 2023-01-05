<template>
<q-page class="q-pa-xs">
  <q-table :rows="cards" title="Registro de targetas" dense flat :rows-per-page-options="[20,50,100,0]" :loading="loading" :columns="cardColumns" :filter="filter">
    <template v-slot:body-cell-actions="props">
      <q-td :props="props" auto-width>
        <q-btn-dropdown label="Opciones" no-caps dense color="primary">
          <q-list>
            <q-item v-if="store.permissions.includes('Editar targeta')" clickable v-close-popup @click="cardsEdit(props.row)">
              <q-item-section avatar>
                <q-icon name="o_edit" />
              </q-item-section>
              <q-item-section>Modificar</q-item-section>
            </q-item>
            <q-item v-if="store.permissions.includes('Eliminar targeta')" clickable v-close-popup @click="cardsDelete(props.row)">
              <q-item-section avatar>
                <q-icon name="o_delete" />
              </q-item-section>
              <q-item-section>Eliminar</q-item-section>
            </q-item>
            <q-item v-if="store.permissions.includes('Subir foto')" clickable v-close-popup @click="cardsPhoto(props.row)">
              <q-item-section avatar>
                <q-icon name="o_photo_camera" />
              </q-item-section>
              <q-item-section>Foto</q-item-section>
            </q-item>
            <q-item v-if="store.permissions.includes('Imprimir tarjeta')" clickable v-close-popup @click="cardsCard(props.row)">
              <q-item-section avatar>
                <q-icon name="credit_card" />
              </q-item-section>
              <q-item-section>Tarjeta</q-item-section>
            </q-item>
          </q-list>
        </q-btn-dropdown>
      </q-td>
    </template>
    <template v-slot:body-cell-photo="props">
      <q-td :props="props" auto-width>
        <a :href="`${url}../../images/${props.row.photo}`" target="_blank"><q-img :src="`${url}../../images/${props.row.photo}`" style="width: 35px; height: 35px;" /></a>
      </q-td>
    </template>
    <template v-slot:top-right>
      <div class="row">
        <div class="col-1 flex flex-center">
          <q-btn @click="exportTable" icon="download" dense no-caps :loading="loading" color="info"/>
        </div>
        <div class="col-4 flex flex-center">
          <q-btn @click="cardsAdd" color="primary" icon="add_circle_outline" dense label="Registrar" v-if="store.permissions.includes('Crear targeta')" no-caps :loading="loading" />
        </div>
        <div class="col-1 flex flex-center">
          <q-btn @click="cardsGet" icon="refresh" flat no-caps dense :loading="loading" />
        </div>

        <div class="col-6 flex flex-center">
          <q-input dense outlined v-model="filter" label="Buscar" clearable>
            <template v-slot:append>
              <q-icon name="search" />
            </template>
          </q-input>
        </div>
      </div>
    </template>
  </q-table>
  <q-dialog v-model="cardDialog">
    <q-card style="width: 700px;max-width: 85vh">
      <q-card-section class="row items-center q-pb-none">
        <div class="text-h6">{{ cardCreate?'Crear':'Modificar'}} tarjeta</div>
        <q-space />
        <q-btn icon="close" flat round dense v-close-popup />
      </q-card-section>
      <q-card-section class="q-pt-xs">
        <q-form @submit="cardsCreate" class="">
          <div class="row">
            <div class="col-12 col-md-4">
              <q-input dense outlined v-model="card.ci" label="CI/NIT" hint="" />
            </div>
            <div class="col-12 col-md-8">
              <q-input dense outlined v-model="card.name" label="Nombre Completo" hint="" required />
            </div>
            <div class="col-12 col-md-4">
              <q-input dense outlined v-model="card.codeTarget" label="Código de tarjeta" hint="" required />
            </div>
            <div class="col-12 col-md-4">
              <q-input dense outlined v-model="card.birthday" label="Fecha de nacimiento" hint="" type="date" />
            </div>
            <div class="col-12 col-md-4">
              <q-input dense outlined v-model="age" label="Edad" hint="" readonly />
            </div>
            <div class="col-12 col-md-6">
              <q-select dense outlined v-model="card.type" label="Tipo" hint="" :options="['REGULARES','VACACIONES']"/>
            </div>
            <div class="col-12 col-md-6">
              <q-select dense outlined v-model="card.days" label="Días" hint="" :options="days" />
            </div>
            <div class="col-12 col-md-6">
              <q-input dense outlined v-model="card.dateIni" label="Fecha de inicio" hint="" type="date" />
            </div>
            <div class="col-12 col-md-6">
              <q-input dense outlined v-model="card.dateEnd" label="Fecha de fin" hint="" type="date" />
            </div>
            <div class="col-12 col-md-4">
              <q-input dense outlined v-model="card.phone" label="Teléfono/Celular" hint="" required />
            </div>
            <div class="col-12 col-md-4">
              <q-select dense outlined v-model="card.schedule" label="Horario" hint="" :options="schedules" required />
            </div>
            <div class="col-12 col-md-4">
              <q-input mask="#" reverse-fill-mask dense outlined v-model="card.amount" label="Monto" hint=""  required />
            </div>
            <div class="col-12 col-md-6">
              <q-input dense outlined v-model="card.observation" label="Observación" hint="" type="textarea" />
            </div>
            <div class="col-12 col-md-12">
              <q-input dense outlined v-model="card.code" label="Código" hint="" clearable required/>
            </div>
          </div>
          <div class="row justify-end">
            <q-btn label="Cancelar" :loading="loading" color="red" icon="o_delete" no-caps v-close-popup />
            <q-btn :label="cardCreate?'Crear':'Modificar'" no-caps :loading="loading" :color="cardCreate?'green':'orange'" icon="o_check_circle" type="submit" />
          </div>
        </q-form>
      </q-card-section>
    </q-card>
  </q-dialog>
  <q-dialog v-model="cardDialogPhoto">
    <q-card style="width: 350px;max-width: 85vh">
      <q-card-section class="row items-center q-pb-none">
        <div class="text-h6">Subir foto</div>
        <q-space />
        <q-btn icon="close" flat round dense v-close-popup />
      </q-card-section>
      <q-card-section class="q-pt-xs">
        <q-form @submit="cardsCreate" class="">
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
                :url="`${url}upload/${card.id}`"
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
import { useCounterStore } from 'stores/example-store'
import xlsx from 'json-as-xlsx'

export default {
  name: 'CardsRegisterItem',
  data () {
    return {
      store: useCounterStore(),
      url: process.env.API,
      PDF: jsPDF,
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
      ],
      cardColumns: [
        { name: 'actions', label: 'Acciones', field: 'actions', align: 'left', sortable: true },
        { name: 'photo', label: 'Foto', field: 'photo', align: 'left', sortable: true },
        { name: 'dateIni', label: 'Fecha de inicio', field: 'dateIni', align: 'left', sortable: true },
        { name: 'number', label: 'Número', field: 'number', align: 'left', sortable: true },
        { name: 'codeTarget', label: 'Código de tarjeta', field: 'codeTarget', align: 'left', sortable: true },
        { name: 'name', label: 'Nombre', field: 'name', align: 'left', sortable: true },
        { name: 'age', label: 'Edad', field: row => this.ageCalculate(row.birthday), align: 'left', sortable: true },
        { name: 'birthday', label: 'Fecha de nacimiento', field: 'birthday', align: 'left', sortable: true },
        { name: 'days', label: 'Días', field: 'days', align: 'left', sortable: true },
        { name: 'phone', label: 'Teléfono/Celular', field: 'phone', align: 'left', sortable: true },
        { name: 'schedule', label: 'Horario', field: 'schedule', align: 'left', sortable: true },
        { name: 'amount', label: 'Monto', field: 'amount', align: 'left', sortable: true }
      ],
      cardDialog: false,
      cardDialogPhoto: false,
      cardCreate: false,
      filter: '',
      cards: [],
      card: {},
      loading: false
    }
  },
  created () {
    this.cardsGet()
  },
  methods: {
    exportTable () {
      const datacaja = [
        {
          sheet: 'Registro',
          columns: [
            { label: 'NUMERO', value: 'number' }, // Top level data
            { label: 'FECHA', value: 'dateIni' }, // Top level data
            { label: 'CODIGO TARJ', value: 'codeTarget' }, // Top level data
            { label: 'NOMBRE', value: 'name' }, // Top level data
            { label: 'EDAD', value: 'age' }, // Top level data
            { label: 'FEC NAC', value: 'birthday' }, // Top level data
            { label: 'DIAS', value: 'days' }, // Top level data
            { label: 'TELEFONO', value: 'phone' }, // Top level data
            { label: 'HORARIO', value: 'schedule' }, // Top level data
            { label: 'MONTO', value: 'amount' } // Top level data

          ],
          content: this.cards
        }

      ]

      const settings = {
        fileName: 'CajaChica', // Name of the resulting spreadsheet
        extraLength: 5, // A bigger number means that columns will be wider
        writeOptions: {} // Style options from https://github.com/SheetJS/sheetjs#writing-options
      }

      xlsx(datacaja, settings) // Will download the excel file
    },
    finishFn (file) {
      this.cardsGet()
      this.cardDialogPhoto = false
    },
    cardsCreate () {
      if (this.card.schedule === '') {
        this.$q.notify({
          message: 'Debe seleccionar un horario',
          color: 'negative',
          icon: 'warning',
          position: 'top'
        })
        return
      }
      this.loading = true
      if (this.cardCreate) {
        this.$api.post('cards', this.card)
          .then(response => {
            this.cards.unshift(response.data)
            this.cardDialog = false
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
        this.$api.put('cards/' + this.card.id, this.card)
          .then(response => {
            this.cards.splice(this.cards.findIndex(item => item.id === this.card.id), 1, response.data)
            this.cardDialog = false
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
    cardsEdit (card) {
      this.card = card
      this.cardDialog = true
      this.cardCreate = false
    },
    cardsCard (card) {
      this.loading = true
      this.$api.get('base64/' + card.photo)
        .then(response => {
          const doc = new this.PDF()
          const img = new Image()
          img.src = 'credential.png'
          doc.addImage(img, 'PNG', 3, 3, 80, 50)
          doc.addImage(response.data, 'JPEG', 5, 14, 28, 28)
          doc.setFontSize(8)
          doc.setFont('helvetica', 'bold')
          doc.text(card.name, 37, 18, { maxWidth: 35, align: 'left' })
          doc.setFontSize(6)
          doc.setFont('helvetica', 'normal')
          doc.text(card.phone, 40, 33)
          doc.text(card.schedule, 40, 37)
          doc.text(card.type, 40, 41)
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
    cardsPhoto (card) {
      this.card = card
      this.cardDialogPhoto = true
    },
    cardsDelete (card) {
      this.$q.dialog({
        title: 'Eliminar tarjeta',
        message: '¿Está seguro de eliminar la tarjeta?',
        ok: 'Eliminar',
        cancel: 'Cancelar',
        persistent: true
      }).onOk(() => {
        this.loading = true
        this.$api.delete('cards/' + card.id)
          .then(response => {
            this.cards.splice(this.cards.indexOf(card), 1)
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
    cardsAdd () {
      this.cardCreate = true
      this.cardDialog = true
      this.card = {
        ci: '',
        code: '',
        dateIni: moment().format('YYYY-MM-DD'),
        dateEnd: moment().add(1, 'month').format('YYYY-MM-DD'),
        codeTarget: '',
        name: '',
        birthday: '2000-01-01',
        phone: '',
        schedule: '',
        amount: '',
        type: 'REGULARES',
        observation: ''
      }
      this.$api.get('maxTarget').then(response => {
        this.card.codeTarget = response.data
      })
    },
    ageCalculate (birthday) {
      return moment().diff(birthday, 'years')
    },
    cardsGet () {
      this.loading = true
      this.$api.get('cards').then((response) => {
        this.cards = response.data
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
  },
  computed: {
    age () {
      return moment().diff(this.card.birthday, 'years')
    },
    days () {
      if (this.card.type === 'REGULARES') {
        // this.card.days = 'LUNES-MIERCOLES'
        return ['LUNES-MIERCOLES', 'MARTE-JUEVES']
      } else {
        // this.card.days = 'MARTES-VIERNES'
        return ['MARTES-VIERNES']
      }
    }
  }
}
</script>

<style scoped>

</style>
