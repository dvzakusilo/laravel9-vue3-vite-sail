<template>
 <div>
     <div class="bg-white dark:bg-gray-800">
         <h1 class="text-gray-900 dark:text-white"></h1>
         <button class="rounded-full " @click="">Запустить</button>
         <p class="text-gray-600 dark:text-gray-300">

         </p>
     </div>
 </div>
</template>

<script>
export default {
    name: "Varnish",
    methods: {
        scan(){
            axios.get('/api/v1/varnish/scan')
                .then(function (response) {
                    console.log(response.data.data)
                })
        }
    }
}
</script>

<style scoped>

</style>
