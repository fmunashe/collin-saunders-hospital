<template>
  <div>
    <Head title="System Not Registered" />

    <Card
      class="flex flex-col items-center justify-center"
      style="min-height: 400px"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="mb-6 text-red-500"
        width="64"
        height="64"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
      >
        <circle cx="12" cy="12" r="10" />
        <line x1="12" y1="8" x2="12" y2="12" />
        <line x1="12" y1="16" x2="12.01" y2="16" />
      </svg>

      <h1 class="dark:text-white text-3xl font-semibold mb-4">
        System License Expired!
      </h1>

      <p class="dark:text-gray-300 text-gray-600 text-lg text-center max-w-md">
        This system's license has expired. Please contact your administrator or the developer Farai on +27 78 386 9706 for support.
      </p>
    </Card>
  </div>
</template>

<script>
export default {
  mounted() {
    //
  },
}
</script>
