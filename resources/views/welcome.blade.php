<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Welcome') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="content-wrapper">
                        <section>
                            <div class="content">
                                <div class="section-container-items">
                                    <div class="container text-center">
                                        <h1>FarmSmart</h1>
                                        <h3>Livestock, Rice, Agriculture, Service</h3>
                                        <a href="#" class="btn btn-success"><i class="bi bi-cart"></i> Buy Now for a Fair Price</a>
                                    </div>
                                </div>
                            </div>  
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
