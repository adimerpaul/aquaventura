<template>
  <q-layout view="lHh Lpr lFf">
    <q-header>
      <q-toolbar>
        <q-btn
          flat
          dense
          round
          icon="menu"
          aria-label="Menu"
          @click="toggleLeftDrawer"
        />

        <q-toolbar-title class="text-bold">
          {{store.user.name}}
        </q-toolbar-title>

        <q-icon
          name="account_circle"
          size="2em"
        >
          <q-menu>
            <q-list>
              <q-item clickable>
                <q-item-section avatar>
                  <q-icon name="account_circle" />
                </q-item-section>
                <q-item-section>Perfil</q-item-section>
              </q-item>
              <q-separator />
              <q-item clickable @click="logout">
                <q-item-section avatar>
                  <q-icon name="exit_to_app" />
                </q-item-section>
                <q-item-section>Salir</q-item-section>
              </q-item>
            </q-list>
          </q-menu>
        </q-icon>
      </q-toolbar>
    </q-header>
    <q-drawer v-model="drawer" show-if-above bordered>
      <q-list>
        <q-item-label header class="text-bold text-center">
          Aqua ventura
        </q-item-label>
        <q-item clickable to="/" exact active-class="bg-primary text-white" >
          <q-item-section avatar>
            <q-icon name="o_home" />
          </q-item-section>
          <q-item-section>Inicio</q-item-section>
        </q-item>
        <q-item clickable to="/cards" v-if="store.permissions.includes('Revisar tarjeta')" active-class="bg-primary text-white">
          <q-item-section avatar>
            <q-icon name="o_credit_card" />
          </q-item-section>
          <q-item-section>Revisar tarjetas</q-item-section>
        </q-item>
        <q-item clickable to="/cardsRegister" v-if="store.permissions.includes('Crear targeta')||store.permissions.includes('Editar targeta')||store.permissions.includes('Eliminar targeta')||store.permissions.includes('Subir foto')||store.permissions.includes('Imprimir tarjeta')" exact active-class="bg-primary text-white" >
          <q-item-section avatar>
            <q-icon name="o_credit_card" />
          </q-item-section>
          <q-item-section>Registrar tarjeta</q-item-section>
        </q-item>
        <q-item clickable to="/history" v-if="store.permissions.includes('Consultar registos')||store.permissions.includes('Descargar registros')" exact active-class="bg-primary text-white" >
          <q-item-section avatar>
            <q-icon name="o_history" />
          </q-item-section>
          <q-item-section>Historial de registros</q-item-section>
        </q-item>
        <q-item clickable to="/users" v-if="store.permissions.includes('Crear usuario')||store.permissions.includes('Editar  usuario')||store.permissions.includes('Eliminar  usuario')" exact active-class="bg-primary text-white" >
          <q-item-section avatar>
            <q-icon name="o_people" />
          </q-item-section>
          <q-item-section>Usuarios</q-item-section>
        </q-item>
        <q-item clickable @click="logout" exact active-class="bg-primary text-white" >
          <q-item-section avatar>
            <q-icon name="o_exit_to_app" />
          </q-item-section>
          <q-item-section>Salir</q-item-section>
        </q-item>
      </q-list>
    </q-drawer>
    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>
<script>
import { useCounterStore } from 'stores/example-store'

export default {
  data () {
    return {
      url: process.env.API,
      drawer: false,
      store: useCounterStore()
    }
  },
  created () {
    // console.log(this.url)
  },
  methods: {
    logout () {
      this.$q.dialog({
        title: 'Cerrar sesión',
        message: '¿Está seguro de cerrar sesión?',
        ok: {
          push: true,
          color: 'primary',
          icon: 'check',
          label: 'Aceptar'
        },
        cancel: {
          push: true,
          color: 'negative',
          icon: 'close'
        }
      }).onOk(() => {
        this.$q.loading.show()
        this.$api.post('logout').then(() => {
          this.$q.loading.hide()
          this.$q.notify({
            message: 'Sesión cerrada',
            color: 'positive',
            icon: 'check_circle',
            position: 'top'
          })
          this.$router.push('/login')
          this.store.user = {}
          this.store.token = ''
          localStorage.removeItem('tokenHospital')
          this.store.isLoggedIn = false
          this.$api.defaults.headers.common.Authorization = ''
        })
        // this.$store.dispatch('logout')
        // this.$router.push('/login')
      })
    },
    toggleLeftDrawer () {
      this.drawer = !this.drawer
    }
  }
}
</script>
