<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $patient->first_name }} {{ $patient->last_name }} - Etiodoc</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100 min-h-screen font-sans" x-data="patientProfile()">

<!-- Header -->
<header class="bg-white shadow-md px-6 py-4 flex justify-between items-center">
    <div class="flex items-center space-x-6">
        <div class="text-xl font-semibold text-gray-800">
           <a href="{{ route('dashboard') }}">
              Etiodoc
          </a>
        </div>
        <nav class="flex space-x-6">
            <a href="#" @click.prevent="showNewPatientForm = true" class="text-gray-700 hover:text-blue-600">Nouveau patient</a>
            <a href="{{ route('comptabilite') }}" class="text-gray-700 hover:text-blue-600">Comptabilité</a>
            <a href="#" class="text-gray-700 hover:text-blue-600">Contact</a>
        </nav>
    </div>

    <div class="flex items-center space-x-4">
        <!-- Barre de recherche -->
        <div class="relative">
            <input 
                type="text" 
                placeholder="Recherche..."
                class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent w-64"
            >
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>

        <!-- Menu utilisateur -->
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="flex items-center text-gray-700 hover:text-blue-600 focus:outline-none">
                <span class="mr-2">Dr. Martin</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="open" @click.away="open = false"
                 class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-2 z-50" style="display:none" x-cloak>
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    Mon compte
                </a>
                <form method="POST" action="{{ route('logout') }}">
                @csrf
                    <button type="submit" 
                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                        Déconnexion
                    </button>
                </form>

            </div>
        </div>
    </div>
</header>

<!-- Contenu principal -->
<div class="px-6 py-8">
    <!-- En-tête patient -->
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">{{ $patient->first_name }} {{ $patient->last_name }}</h1>
        <div class="flex space-x-3">
            <!-- Boutons de consultation -->
            <button @click="startConsultation()" 
                    x-show="!isConsultationActive"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                Démarrer une consultation
            </button>
            
            <button @click="endConsultation()" 
                    x-show="isConsultationActive"
                    class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                Arrêter la consultation
            </button>
        </div>
    </div>

    <!-- Indicateur de consultation active -->
    <div x-show="isConsultationActive" class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-center">
            <div class="animate-pulse bg-red-500 rounded-full w-3 h-3 mr-3"></div>
            <span class="text-blue-800 font-medium">Consultation en cours...</span>
            <span class="ml-auto text-blue-600 text-sm" x-text="'Démarrée à ' + consultationStartTime"></span>
        </div>
    </div>

    <!-- Navigation des onglets -->
    <div class="border-b border-gray-200 mb-6">
        <nav class="flex space-x-8">
            <button @click="activeTab = 'general'" 
                    :class="{'text-blue-600 border-b-2 border-blue-600': activeTab === 'general', 'text-gray-500 hover:text-gray-700': activeTab !== 'general'}"
                    class="py-2 px-1 font-medium">
                Infos générales
            </button>
            <button @click="activeTab = 'antecedents'" 
                    :class="{'text-blue-600 border-b-2 border-blue-600': activeTab === 'antecedents', 'text-gray-500 hover:text-gray-700': activeTab !== 'antecedents'}"
                    class="py-2 px-1 font-medium">
                Antécédents
            </button>
            <button @click="activeTab = 'comptes'" 
                    :class="{'text-blue-600 border-b-2 border-blue-600': activeTab === 'comptes', 'text-gray-500 hover:text-gray-700': activeTab !== 'comptes'}"
                    class="py-2 px-1 font-medium">
                Comptes-rendus médicaux
            </button>
            <button @click="activeTab = 'consultations'" 
                    :class="{'text-blue-600 border-b-2 border-blue-600': activeTab === 'consultations', 'text-gray-500 hover:text-gray-700': activeTab !== 'consultations'}"
                    class="py-2 px-1 font-medium">
                Consultations
            </button>
        </nav>
    </div>

    <!-- Contenu des onglets -->
    <div class="space-y-6">
        <!-- Informations générales -->
        <div x-show="activeTab === 'general'" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-6">Informations personnelles</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Prénom</label>
                    <div class="editable-field" 
                         :class="editMode ? 'edit-mode' : ''"
                         @click="editMode && $refs.firstName.focus()">
                        <input x-ref="firstName"
                               x-model="patientData.firstName"
                               :readonly="!editMode"
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               :class="editMode ? 'bg-white' : 'bg-gray-50'"
                        >
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nom</label>
                    <div class="editable-field" 
                         :class="editMode ? 'edit-mode' : ''"
                         @click="editMode && $refs.lastName.focus()">
                        <input x-ref="lastName"
                               x-model="patientData.lastName"
                               :readonly="!editMode"
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               :class="editMode ? 'bg-white' : 'bg-gray-50'"
                        >
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Téléphone</label>
                    <div class="editable-field" 
                         :class="editMode ? 'edit-mode' : ''"
                         @click="editMode && $refs.phone.focus()">
                        <input x-ref="phone"
                               x-model="patientData.phone"
                               :readonly="!editMode"
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               :class="editMode ? 'bg-white' : 'bg-gray-50'"
                        >
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date de naissance</label>
                    <div class="editable-field" 
                         :class="editMode ? 'edit-mode' : ''"
                         @click="editMode && $refs.birthdate.focus()">
                        <input x-ref="birthdate"
                               x-model="patientData.birthdate"
                               type="date"
                               :readonly="!editMode"
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               :class="editMode ? 'bg-white' : 'bg-gray-50'"
                        >
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <div class="editable-field" 
                         :class="editMode ? 'edit-mode' : ''"
                         @click="editMode && $refs.email.focus()">
                        <input x-ref="email"
                               x-model="patientData.email"
                               type="email"
                               :readonly="!editMode"
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               :class="editMode ? 'bg-white' : 'bg-gray-50'"
                        >
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Adresse</label>
                    <div class="editable-field" 
                         :class="editMode ? 'edit-mode' : ''"
                         @click="editMode && $refs.address.focus()">
                        <textarea x-ref="address"
                                  x-model="patientData.address"
                                  :readonly="!editMode"
                                  rows="3"
                                  class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                  :class="editMode ? 'bg-white' : 'bg-gray-50'"
                        ></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Antécédents -->
        <div x-show="activeTab === 'antecedents'" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-800">Antécédents médicaux</h3>
                <button @click="addAntecedent()" 
                        x-show="editMode || isConsultationActive"
                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                    + Ajouter
                </button>
            </div>
            
            <div class="space-y-4">
                <template x-for="(antecedent, index) in antecedents" :key="index">
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div class="flex justify-between items-start mb-2">
                            <div class="editable-field w-full mr-4" 
                                 :class="(editMode || isConsultationActive) ? 'edit-mode' : ''"
                                 @click="(editMode || isConsultationActive) && $refs['antTitle' + index][0].focus()">
                                <input :x-ref="'antTitle' + index"
                                       x-model="antecedent.title"
                                       :readonly="!(editMode || isConsultationActive)"
                                       class="w-full font-medium p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       :class="(editMode || isConsultationActive) ? 'bg-white' : 'bg-transparent border-transparent'"
                                       placeholder="Titre de l'antécédent"
                                >
                            </div>
                            <button x-show="editMode || isConsultationActive" @click="removeAntecedent(index)"
                                    class="text-red-500 hover:text-red-700 transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                        <div class="editable-field" 
                             :class="(editMode || isConsultationActive) ? 'edit-mode' : ''"
                             @click="(editMode || isConsultationActive) && $refs['antDesc' + index][0].focus()">
                            <textarea :x-ref="'antDesc' + index"
                                      x-model="antecedent.description"
                                      :readonly="!(editMode || isConsultationActive)"
                                      rows="2"
                                      class="w-full text-gray-600 p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                      :class="(editMode || isConsultationActive) ? 'bg-white' : 'bg-transparent border-transparent'"
                                      placeholder="Description de l'antécédent"
                            ></textarea>
                        </div>
                    </div>
                </template>
                
                <div x-show="antecedents.length === 0" class="text-center py-8 text-gray-500">
                    Aucun antécédent médical enregistré
                </div>
            </div>
        </div>

        <!-- Comptes-rendus médicaux -->
        <div x-show="activeTab === 'comptes'" class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-800">Historique des consultations</h3>
                    <div class="text-sm text-gray-500" x-text="getSortedConsultations().length + ' consultation(s)'"></div>
                </div>
                
                <!-- Liste des consultations -->
                <div class="space-y-4">
                    <template x-for="(consultation, index) in getSortedConsultations()" :key="consultation.id">
                        <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden">
                            <!-- En-tête de consultation -->
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-4 cursor-pointer"
                                 @click="toggleConsultationDetails(consultation.id)">
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center space-x-4">
                                        <h4 class="font-semibold" x-text="'Séance du ' + formatDate(consultation.consultation_date)"></h4>
                                        <span class="bg-white bg-opacity-20 px-3 py-1 rounded-full text-sm">
                                            Dr. MARTIN Paul
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span x-text="getConsultationStatus(consultation.status)" 
                                              class="bg-white bg-opacity-20 px-2 py-1 rounded text-sm"></span>
                                        <svg :class="expandedConsultations.includes(consultation.id) ? 'rotate-180' : ''" 
                                             class="w-5 h-5 transform transition-transform duration-200" 
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                                
                                <!-- Aperçu rapide -->
                                <div class="mt-2 text-blue-100">
                                    <p class="text-sm truncate" x-show="consultation.motif" x-text="'Motif: ' + consultation.motif"></p>
                                    <p class="text-sm truncate" x-show="consultation.etiopathic_diagnosis" x-text="'Diagnostic: ' + consultation.etiopathic_diagnosis"></p>
                                </div>
                            </div>
                            
                            <!-- Détails de consultation -->
                            <div x-show="expandedConsultations.includes(consultation.id)" 
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 max-h-0"
                                 x-transition:enter-end="opacity-100 max-h-none"
                                 x-transition:leave="transition ease-in duration-200"
                                 x-transition:leave-start="opacity-100 max-h-none"
                                 x-transition:leave-end="opacity-0 max-h-0"
                                 class="p-6 space-y-6">
                                
                                <!-- Motif -->
                                <div x-show="consultation.motif" class="bg-red-50 border border-red-200 rounded-lg p-4">
                                    <h5 class="text-red-800 font-medium text-sm uppercase tracking-wide mb-2">Motif</h5>
                                    <p class="text-gray-700 whitespace-pre-line" x-text="consultation.motif"></p>
                                </div>
                                
                                <!-- Symptômes -->
                                <div x-show="consultation.symptoms" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                    <h5 class="text-yellow-800 font-medium text-sm uppercase tracking-wide mb-2">Symptômes</h5>
                                    <p class="text-gray-700 whitespace-pre-line" x-text="consultation.symptoms"></p>
                                </div>
                                
                                <!-- Examen médical -->
                                <div x-show="consultation.clinical_examination" class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                    <h5 class="text-gray-600 font-medium text-sm uppercase tracking-wide mb-2">Examen médical</h5>
                                    <p class="text-gray-700 whitespace-pre-line" x-text="consultation.clinical_examination"></p>
                                </div>
                                
                                <!-- Diagnostic -->
                                <div x-show="consultation.etiopathic_diagnosis" class="bg-blue-500 text-white rounded-lg p-4">
                                    <h5 class="font-medium text-sm uppercase tracking-wide mb-2">Diagnostic Étiopathique</h5>
                                    <p class="whitespace-pre-line" x-text="consultation.etiopathic_diagnosis"></p>
                                </div>
                                
                                <!-- Plan de traitement -->
                                <div x-show="consultation.treatment_plan" class="bg-green-50 border border-green-200 rounded-lg p-4">
                                    <h5 class="text-green-800 font-medium text-sm uppercase tracking-wide mb-2">Plan de traitement</h5>
                                    <p class="text-gray-700 whitespace-pre-line" x-text="consultation.treatment_plan"></p>
                                </div>
                                
                                <!-- Recommandations -->
                                <div x-show="consultation.recommendations" class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                                    <h5 class="text-purple-800 font-medium text-sm uppercase tracking-wide mb-2">Recommandations</h5>
                                    <p class="text-gray-700 whitespace-pre-line" x-text="consultation.recommendations"></p>
                                </div>
                                
                                <!-- Notes -->
                                <div x-show="consultation.notes" class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                    <h5 class="text-gray-600 font-medium text-sm uppercase tracking-wide mb-2">Notes</h5>
                                    <p class="text-gray-700 whitespace-pre-line" x-text="consultation.notes"></p>
                                </div>
                                
                                <!-- Informations de paiement -->
                                <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <h5 class="text-indigo-800 font-medium text-sm uppercase tracking-wide mb-1">Paiement</h5>
                                            <p class="text-gray-700">
                                                <span x-text="consultation.price > 0 ? consultation.price + '€' : 'Tarif non défini'"></span>
                                                - 
                                                <span :class="getPaymentStatusColor(consultation.payment_status)" 
                                                      x-text="getPaymentStatusText(consultation.payment_status)"></span>
                                            </p>
                                        </div>
                                        <button x-show="consultation.payment_status === 'pending'" 
                                                @click="openPaymentModal(consultation)"
                                                class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm transition-colors">
                                            Marquer payé
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Métadonnées -->
                                <div class="bg-gray-100 rounded-lg p-3 text-sm text-gray-600">
                                    <div class="flex justify-between">
                                        <span>Consultation #<span x-text="consultation.id"></span></span>
                                        <span x-text="'Créée le ' + formatDateTime(consultation.consultation_date)"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    
                    <!-- Message si pas de consultations -->
                    <div x-show="getSortedConsultations().length === 0" class="text-center py-12 text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="text-lg font-medium mb-2">Aucune consultation enregistrée</h3>
                        <p class="text-sm">Les consultations apparaîtront ici une fois créées.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Consultations -->
        <div x-show="activeTab === 'consultations'" class="space-y-6">
            <!-- Consultation en cours ou récente -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800" 
                        x-text="isConsultationActive ? 'Consultation en cours' : (currentConsultationData.consultation_date ? 'Séance du ' + formatDate(currentConsultationData.consultation_date) + ' par Dr. MARTIN Paul' : 'Nouvelle consultation')"></h3>
                </div>
                
                <!-- Statut consultation -->
                <div :class="isConsultationActive ? 'bg-orange-50 border-orange-200' : 'bg-green-50 border-green-200'" 
                     class="border rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" :class="isConsultationActive ? 'text-orange-500' : 'text-green-500'" 
                             fill="currentColor" viewBox="0 0 20 20">
                            <path x-show="!isConsultationActive" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            <path x-show="isConsultationActive" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                        </svg>
                        <span :class="isConsultationActive ? 'text-orange-800' : 'text-green-800'" 
                              class="font-medium" 
                              x-text="isConsultationActive ? 'Consultation en cours' : 'Consultation de suivi'"></span>
                    </div>
                </div>

                <!-- Motif -->
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <h4 class="text-red-800 font-medium text-sm uppercase tracking-wide mb-3">Motif</h4>
                    <div x-show="!isConsultationActive" class="text-gray-700" 
                         x-text="currentConsultationData.symptoms || 'Aucun symptôme enregistré'">
                    </div>
                    <div x-show="isConsultationActive" class="editable-field edit-mode">
                        <textarea x-ref="symptoms"
                                  x-model="consultationData.symptoms"
                                  rows="3"
                                  class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none bg-white"
                                  placeholder="Symptômes observés..."
                        ></textarea>
                    </div>
                </div>



                <!-- Diagnostic Étiopathique -->
                <div class="bg-blue-500 text-white rounded-lg p-4 mb-6">
                    <h3 class="font-medium mb-3">Diagnostic Étiopathique</h3>
                    <div x-show="!isConsultationActive" class="text-white" 
                         x-text="currentConsultationData.etiopathic_diagnosis || 'Aucun diagnostic enregistré'">
                    </div>
                    <div x-show="isConsultationActive" class="editable-field edit-mode">
                        <textarea x-ref="diagnostic"
                                  x-model="consultationData.diagnostic"
                                  rows="4"
                                  class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none text-gray-800 bg-white"
                                  placeholder="Diagnostic étiopathique..."
                        ></textarea>
                    </div>
                </div>

                <!-- Squelette et Examen médical côte à côte -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Squelette réaliste (SVG) -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-gray-800 font-semibold">Squelette réaliste</h4>
                            <div class="text-sm text-gray-500">Cliquez pour changer la couleur (sauvegardé)</div>
                        </div>

                    @php
                        // Supporte plusieurs chemins possibles selon orthographe (image/images) et squelette/squellete
                        $dirs = [
                            public_path('image/squelette'),
                            public_path('image/squellete'),
                            public_path('images/squelette'),
                            public_path('images/squellete'),
                        ];
                        $svgs = [];
                        $paths = [];
                        foreach ($dirs as $dir) {
                            if (is_dir($dir)) {
                                foreach (scandir($dir) as $file) {
                                    $lower = strtolower($file);
                                    if (substr($lower, -4) === '.svg' && is_file($dir . DIRECTORY_SEPARATOR . $file)) {
                                        $svgs[] = $file;
                                        $paths[$file] = $dir . DIRECTORY_SEPARATOR . $file;
                                    }
                                }
                            }
                        }
                        // Tenter d'ordonner face/dos en premier si présents
                        usort($svgs, function($a, $b) {
                            $order = ['front','face','anterieur','anterior','dos','back','posterieur','posterior'];
                            $ia = 100; $ib = 100;
                            foreach ($order as $i => $key) { if (stripos($a, $key) !== false) { $ia = $i; break; } }
                            foreach ($order as $i => $key) { if (stripos($b, $key) !== false) { $ib = $i; break; } }
                            if ($ia === $ib) return strcmp($a, $b);
                            return $ia <=> $ib;
                        });
                    @endphp

                    @if (!empty($svgs))
                        @php
                            // Filtrer les parties "vue de face" si mentionnées dans le nom, sinon garder tout
                            $frontKeywords = ['front','face','anterieur','anterior'];
                            $frontFiles = [];
                            foreach ($svgs as $file) {
                                $isFront = false;
                                foreach ($frontKeywords as $kw) { if (stripos($file, $kw) !== false) { $isFront = true; break; } }
                                if ($isFront) { $frontFiles[] = $file; }
                            }
                            if (empty($frontFiles)) { $frontFiles = $svgs; }

                            // Catégoriser par parties: tête, torse, bras G/D, bassin, jambe G/D
                            $parts = [
                                'head' => [], 'torso' => [], 'arm_left' => [], 'arm_right' => [],
                                'pelvis' => [], 'leg_left' => [], 'leg_right' => [], 'unknown' => []
                            ];
                            $isLeft = function($name){
                                $leftKeys = ['gauche','left','_l','-l',' l.',' l_',' l-'];
                                foreach ($leftKeys as $k) { if (stripos($name, $k) !== false) return true; }
                                return false;
                            };
                            $isRight = function($name){
                                $rightKeys = ['droit','droite','right','_r','-r',' r.',' r_',' r-'];
                                foreach ($rightKeys as $k) { if (stripos($name, $k) !== false) return true; }
                                return false;
                            };
                            foreach ($frontFiles as $file) {
                                $low = strtolower($file);
                                if (str_contains($low,'tete') || str_contains($low,'head') || str_contains($low,'crane')) {
                                    $parts['head'][] = $file; continue;
                                }
                                if (str_contains($low,'thorax') || str_contains($low,'torse') || str_contains($low,'buste') || str_contains($low,'chest')) {
                                    $parts['torso'][] = $file; continue;
                                }
                                if (str_contains($low,'abdomen') || str_contains($low,'ventre')) { $parts['torso'][] = $file; continue; }
                                if (str_contains($low,'pelvis') || str_contains($low,'bassin')) { $parts['pelvis'][] = $file; continue; }
                                if (str_contains($low,'bras') || str_contains($low,'arm')) {
                                    if ($isLeft($low)) { $parts['arm_left'][] = $file; } elseif ($isRight($low)) { $parts['arm_right'][] = $file; } else { $parts['arm_left'][] = $file; }
                                    continue;
                                }
                                if (str_contains($low,'jambe') || str_contains($low,'leg') || str_contains($low,'pied') || str_contains($low,'foot')) {
                                    if ($isLeft($low)) { $parts['leg_left'][] = $file; } elseif ($isRight($low)) { $parts['leg_right'][] = $file; } else { $parts['leg_left'][] = $file; }
                                    continue;
                                }
                                // Par défaut, on classe en "unknown" pour affichage à la fin
                                $parts['unknown'][] = $file;
                            }
                            // Si on a des bras/jambes non appariés, essayer d'équilibrer
                            if (count($parts['arm_left']) > 1 && count($parts['arm_right']) === 0) {
                                $move = array_splice($parts['arm_left'], 1);
                                $parts['arm_right'] = array_merge($parts['arm_right'], $move);
                            }
                            if (count($parts['leg_left']) > 1 && count($parts['leg_right']) === 0) {
                                $move = array_splice($parts['leg_left'], 1);
                                $parts['leg_right'] = array_merge($parts['leg_right'], $move);
                            }
                        @endphp

                        <div class="grid grid-cols-1 gap-4">
                            <div class="bg-gray-50 rounded-lg border border-gray-200 p-3 overflow-hidden">
                                <h5 class="text-gray-700 font-medium mb-3">Vue de face</h5>
                                <div class="skeleton-figure mx-auto">
                                    <!-- Tête -->
                                    <div class="flex justify-center">
                                        <div class="skeleton-part skeleton-head">
                                            @foreach ($parts['head'] as $file)
                                                @php $p = $paths[$file] ?? null; @endphp
                                                <div class="skeleton-svg skeleton-colorable" x-data="skeletonColor('{{ $patient->id }}','{{ $file }}')" @click="next()" :style="`--sk-color: ${color}`">
                                                    {!! $p && is_readable($p) ? file_get_contents($p) : '' !!}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Ligne bras / torse -->
                                    <div class="skeleton-row mt-1">
                                        <div class="skeleton-part skeleton-arm">
                                            @foreach ($parts['arm_right'] as $file)
                                                @php $p = $paths[$file] ?? null; @endphp
                                                <div class="skeleton-svg skeleton-colorable" x-data="skeletonColor('{{ $patient->id }}','{{ $file }}')" @click="next()" :style="`--sk-color: ${color}`">
                                                    {!! $p && is_readable($p) ? file_get_contents($p) : '' !!}
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="skeleton-part skeleton-torso">
                                            @foreach ($parts['torso'] as $file)
                                                @php $p = $paths[$file] ?? null; @endphp
                                                <div class="skeleton-svg skeleton-colorable" x-data="skeletonColor('{{ $patient->id }}','{{ $file }}')" @click="next()" :style="`--sk-color: ${color}`">
                                                    {!! $p && is_readable($p) ? file_get_contents($p) : '' !!}
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="skeleton-part skeleton-arm">
                                            @foreach ($parts['arm_left'] as $file)
                                                @php $p = $paths[$file] ?? null; @endphp
                                                <div class="skeleton-svg skeleton-colorable" x-data="skeletonColor('{{ $patient->id }}','{{ $file }}')" @click="next()" :style="`--sk-color: ${color}`">
                                                    {!! $p && is_readable($p) ? file_get_contents($p) : '' !!}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Bassin -->
                                    <div class="flex justify-center mt-1">
                                        <div class="skeleton-part skeleton-pelvis">
                                            @foreach ($parts['pelvis'] as $file)
                                                @php $p = $paths[$file] ?? null; @endphp
                                                <div class="skeleton-svg skeleton-colorable" x-data="skeletonColor('{{ $patient->id }}','{{ $file }}')" @click="next()" :style="`--sk-color: ${color}`">
                                                    {!! $p && is_readable($p) ? file_get_contents($p) : '' !!}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Jambes -->
                                    <div class="flex items-start justify-center gap-2 mt-1">
                                        <div class="skeleton-part skeleton-leg">
                                            @foreach ($parts['leg_left'] as $file)
                                                @php $p = $paths[$file] ?? null; @endphp
                                                <div class="skeleton-svg skeleton-colorable" x-data="skeletonColor('{{ $patient->id }}','{{ $file }}')" @click="next()" :style="`--sk-color: ${color}`">
                                                    {!! $p && is_readable($p) ? file_get_contents($p) : '' !!}
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="skeleton-part skeleton-leg">
                                            @foreach ($parts['leg_right'] as $file)
                                                @php $p = $paths[$file] ?? null; @endphp
                                                <div class="skeleton-svg skeleton-colorable" x-data="skeletonColor('{{ $patient->id }}','{{ $file }}')" @click="next()" :style="`--sk-color: ${color}`">
                                                    {!! $p && is_readable($p) ? file_get_contents($p) : '' !!}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    @if (!empty($parts['unknown']))
                                    <!-- Autres parties (non reconnues) -->
                                    <div class="mt-2 space-y-2">
                                        @foreach ($parts['unknown'] as $file)
                                            @php $p = $paths[$file] ?? null; @endphp
                                            <div class="skeleton-part">
                                                <div class="skeleton-svg skeleton-colorable" x-data="skeletonColor('{{ $patient->id }}','{{ $file }}')" @click="next()" :style="`--sk-color: ${color}`">
                                                    {!! $p && is_readable($p) ? file_get_contents($p) : '' !!}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-gray-500 text-sm">Placez vos SVGs dans public/image/squelette (ou public/image/squellete)</div>
                    @endif
                    </div>

                    <!-- Examen médical -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h4 class="text-gray-600 font-medium text-center mb-6">Examen médical</h4>
                        
                        <div x-show="!isConsultationActive" class="text-gray-700 whitespace-pre-line" 
                             x-text="currentConsultationData.clinical_examination || 'Aucun examen enregistré'">
                        </div>
                        <div x-show="isConsultationActive" class="editable-field edit-mode">
                            <textarea x-ref="examenMedical"
                                      x-model="consultationData.examenMedical"
                                      rows="8"
                                      class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none bg-white"
                                      placeholder="Détails de l'examen médical...">
                            </textarea>
                        </div>
                    </div>
                </div>

                <!-- Plan de traitement -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6" x-show="currentConsultationData.treatment_plan || isConsultationActive">
                    <h4 class="text-green-800 font-medium text-sm uppercase tracking-wide mb-3">Plan de traitement</h4>
                    <div x-show="!isConsultationActive" class="text-gray-700 whitespace-pre-line" 
                         x-text="currentConsultationData.treatment_plan || 'Aucun plan de traitement'">
                    </div>
                    <div x-show="isConsultationActive" class="editable-field edit-mode">
                        <textarea x-ref="treatmentPlan"
                                  x-model="consultationData.treatmentPlan"
                                  rows="4"
                                  class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none bg-white"
                                  placeholder="Plan de traitement..."
                        ></textarea>
                    </div>
                </div>

                <!-- Recommandations -->
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-6" x-show="currentConsultationData.recommendations || isConsultationActive">
                    <h4 class="text-purple-800 font-medium text-sm uppercase tracking-wide mb-3">Recommandations</h4>
                    <div x-show="!isConsultationActive" class="text-gray-700 whitespace-pre-line" 
                         x-text="currentConsultationData.recommendations || 'Aucune recommandation'">
                    </div>
                    <div x-show="isConsultationActive" class="editable-field edit-mode">
                        <textarea x-ref="recommendations"
                                  x-model="consultationData.recommendations"
                                  rows="3"
                                  class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none bg-white"
                                  placeholder="Recommandations pour le patient..."
                        ></textarea>
                    </div>
                </div>

                <!-- Notes -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4" x-show="currentConsultationData.notes || isConsultationActive">
                    <h4 class="text-gray-600 font-medium text-sm uppercase tracking-wide mb-3">Notes</h4>
                    <div x-show="!isConsultationActive" class="text-gray-700 whitespace-pre-line" 
                         x-text="currentConsultationData.notes || 'Aucune note'">
                    </div>
                    <div x-show="isConsultationActive" class="editable-field edit-mode">
                        <textarea x-ref="notes"
                                  x-model="consultationData.notes"
                                  rows="3"
                                  class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none bg-white"
                                  placeholder="Notes additionnelles..."
                        ></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nouveau patient -->
<div x-show="showNewPatientForm" x-cloak
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md">
        <h2 class="text-lg font-bold mb-4">Ajouter un patient</h2>
        <form @submit.prevent="addPatient()">
            <div class="space-y-4">
                <input type="text" x-model="newPatient.firstName" placeholder="Prénom"
                       class="w-full border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                <input type="text" x-model="newPatient.lastName" placeholder="Nom"
                       class="w-full border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                <input type="tel" x-model="newPatient.phone" placeholder="Téléphone"
                       class="w-full border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <input type="date" x-model="newPatient.birthdate"
                       class="w-full border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
            </div>
            
            <div class="flex justify-end space-x-2 mt-6">
                <button type="button" @click="showNewPatientForm = false"
                        class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors duration-200">
                    Annuler
                </button>
                <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    Ajouter
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function patientProfile() {
    return {
        activeTab: 'consultations',
        editMode: false,
        isConsultationActive: false,
        consultationStartTime: '',
        showNewPatientForm: false,
        expandedConsultations: [], // Pour gérer l'expansion des consultations dans les comptes-rendus
        
        // Données du patient depuis Laravel
        patientData: {
            id: {{ $patient->id }},
            firstName: '{{ $patient->first_name }}',
            lastName: '{{ $patient->last_name }}',
            phone: '{{ $patient->phone ?? "" }}',
            birthdate: '{{ $patient->birthdate }}',
            email: '{{ $patient->email ?? "" }}',
            address: '{{ $patient->address ?? "" }}'
        },
        
        // Données de la consultation courante/récente depuis Laravel
        currentConsultationData: {
            id: {{ $currentConsultation->id ?? 'null' }},
            consultation_date: '{{ $currentConsultation->consultation_date ?? "" }}',
            motif: `{{ $currentConsultation->motif ?? "" }}`,
            symptoms: `{{ $currentConsultation->symptoms ?? "" }}`,
            clinical_examination: `{{ $currentConsultation->clinical_examination ?? "" }}`,
            etiopathic_diagnosis: `{{ $currentConsultation->etiopathic_diagnosis ?? "" }}`,
            treatment_plan: `{{ $currentConsultation->treatment_plan ?? "" }}`,
            recommendations: `{{ $currentConsultation->recommendations ?? "" }}`,
            notes: `{{ $currentConsultation->notes ?? "" }}`,
            status: '{{ $currentConsultation->status ?? "normale" }}',
            price: {{ $currentConsultation->price ?? 0 }},
            payment_status: '{{ $currentConsultation->payment_status ?? "pending" }}'
        },
        
        // Données pour la consultation en cours (copie modifiable)
        consultationData: {
            motif: '',
            symptoms: '',
            examenMedical: '',
            diagnostic: '',
            treatmentPlan: '',
            recommendations: '',
            notes: '',
            status: 'normale'
        },
        
        // Liste de toutes les consultations depuis Laravel
        allConsultations: [
            @if(isset($consultations))
                @foreach($consultations as $consultation)
                {
                    id: {{ $consultation->id }},
                    consultation_date: '{{ $consultation->consultation_date }}',
                    motif: `{{ $consultation->motif }}`,
                    symptoms: `{{ $consultation->symptoms }}`,
                    clinical_examination: `{{ $consultation->clinical_examination }}`,
                    etiopathic_diagnosis: `{{ $consultation->etiopathic_diagnosis }}`,
                    treatment_plan: `{{ $consultation->treatment_plan }}`,
                    recommendations: `{{ $consultation->recommendations }}`,
                    notes: `{{ $consultation->notes }}`,
                    status: '{{ $consultation->status }}',
                    price: {{ $consultation->price ?? 0 }},
                    payment_status: '{{ $consultation->payment_status }}'
                }@if(!$loop->last),@endif
                @endforeach
            @else
                // Données d'exemple pour la démonstration
                {
                    id: 1,
                    consultation_date: '2024-08-15 14:30:00',
                    motif: 'Douleurs cervicales persistantes depuis 3 semaines suite à un faux mouvement',
                    symptoms: 'Douleurs cervicales, raideur matinale, céphalées de tension',
                    clinical_examination: 'Limitation de la mobilité cervicale en rotation droite.\nTension palpable des muscles sous-occipitaux.\nTest de compression cervicale positif.',
                    etiopathic_diagnosis: 'Dysfonction articulaire C1-C2 avec spasme musculaire secondaire des muscles sous-occipitaux',
                    treatment_plan: 'Manipulation douce C1-C2\nTechniques de relâchement musculaire\n3 séances sur 10 jours',
                    recommendations: 'Éviter les mouvements brusques du cou\nApplication de chaleur 15min matin et soir\nExercices d\'étirement cervical doux',
                    notes: 'Patient très tendu, sensible à la palpation. Amélioration notable après traitement.',
                    status: 'normale',
                    price: 55,
                    payment_status: 'paid'
                },
                {
                    id: 2,
                    consultation_date: '2024-08-10 09:15:00',
                    motif: 'Suivi douleurs lombaires chroniques',
                    symptoms: 'Lombalgie chronique, difficulté à rester debout longtemps',
                    clinical_examination: 'Flexion lombaire limitée à 60°.\nDouleur à la palpation L4-L5.\nTest de Lasègue négatif.',
                    etiopathic_diagnosis: 'Restriction de mobilité L4-L5 avec contracture paravertébrale',
                    treatment_plan: 'Mobilisation L4-L5\nÉtirements des psoas\nRenforcement du transverse',
                    recommendations: 'Maintenir activité physique régulière\nÉviter port de charges lourdes\nSurélever les jambes en position allongée',
                    notes: 'Évolution favorable. Patient plus mobile qu\'à la séance précédente.',
                    status: 'normale',
                    price: 55,
                    payment_status: 'pending'
                },
                {
                    id: 3,
                    consultation_date: '2024-08-05 16:45:00',
                    motif: 'Première consultation - douleurs d\'épaule droite',
                    symptoms: 'Douleur épaule droite, limitation des mouvements en élévation',
                    clinical_examination: 'Limitation douloureuse en abduction à 90°.\nTest de Neer positif.\nForce musculaire conservée.',
                    etiopathic_diagnosis: 'Conflit sous-acromial avec inflammation de la bourse séreuse',
                    treatment_plan: 'Mobilisation douce gléno-humérale\nTechniques de décompression\nTraitement des trigger points',
                    recommendations: 'Repos relatif de l\'épaule\nGlace après activité\nExercices pendulaires 3x/jour',
                    notes: 'Première séance. Patient motivé pour le traitement.',
                    status: 'premiere',
                    price: 65,
                    payment_status: 'paid'
                }
            @endif
        ],
        
        // Antécédents (à récupérer depuis une table medical_history si elle existe)
        antecedents: [
            // Ces données pourraient venir de la BDD aussi
            {
                title: 'Accident de voiture - whiplash',
                description: 'Traumatisme cervical suite à accident de la route en 2019. Séquelles de tensions cervicales.'
            },
            {
                title: 'Migraines récurrentes',
                description: 'Céphalées de type tension depuis l\'adolescence, aggravées en période de stress.'
            }
        ],
        
        newPatient: {
            firstName: '',
            lastName: '',
            phone: '',
            birthdate: ''
        },
        
        init() {
            console.log('Application initialisée');
            console.log('Données patient:', this.patientData);
            console.log('Consultation courante:', this.currentConsultationData);
            console.log('Toutes les consultations:', this.allConsultations);
            
            // Initialiser les données de consultation avec les valeurs existantes
            this.consultationData = {
                motif: this.currentConsultationData.motif || '',
                symptoms: this.currentConsultationData.symptoms || '',
                examenMedical: this.currentConsultationData.clinical_examination || '',
                diagnostic: this.currentConsultationData.etiopathic_diagnosis || '',
                treatmentPlan: this.currentConsultationData.treatment_plan || '',
                recommendations: this.currentConsultationData.recommendations || '',
                notes: this.currentConsultationData.notes || '',
                status: this.currentConsultationData.status || 'normale'
            };
        },
        
        // Utilitaires couleur squelette (exposés pour Alpine x-data via window)
        
        // Nouvelles méthodes pour les comptes-rendus
        getSortedConsultations() {
            return [...this.allConsultations].sort((a, b) => {
                return new Date(b.consultation_date) - new Date(a.consultation_date);
            });
        },
        
        toggleConsultationDetails(consultationId) {
            const index = this.expandedConsultations.indexOf(consultationId);
            if (index > -1) {
                this.expandedConsultations.splice(index, 1);
            } else {
                this.expandedConsultations.push(consultationId);
            }
        },
        
        getConsultationStatus(status) {
            const statusMap = {
                'normale': 'Consultation',
                'premiere': 'Première consultation',
                'urgence': 'Urgence',
                'suivi': 'Suivi'
            };
            return statusMap[status] || 'Consultation';
        },
        
        getPaymentStatusText(status) {
            const statusMap = {
                'pending': 'En attente',
                'paid': 'Payé',
                'cancelled': 'Annulé'
            };
            return statusMap[status] || 'En attente';
        },
        
        getPaymentStatusColor(status) {
            const colorMap = {
                'pending': 'text-orange-600',
                'paid': 'text-green-600',
                'cancelled': 'text-red-600'
            };
            return colorMap[status] || 'text-gray-600';
        },
        
        async updatePaymentStatus(consultationId, newStatus) {
            try {
                // Trouver la consultation dans la liste
                const consultation = this.allConsultations.find(c => c.id === consultationId);
                if (consultation) {
                    consultation.payment_status = newStatus;
                }
                
                // Envoyer la mise à jour au serveur
                const response = await fetch(`/consultations/${consultationId}/payment`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({ payment_status: newStatus })
                });
                
                if (!response.ok) {
                    throw new Error('Erreur serveur: ' + response.status);
                }
                
                console.log('Statut de paiement mis à jour avec succès');
            } catch (error) {
                console.error('Erreur lors de la mise à jour du paiement:', error);
                alert('Erreur lors de la mise à jour du statut de paiement');
            }
        },
        
        formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('fr-FR', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        },
        
        formatDateTime(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('fr-FR', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },
        
        toggleEditMode() {
            if (this.editMode) {
                this.savePatientData();
            }
            this.editMode = !this.editMode;
        },
        
        startConsultation() {
            this.isConsultationActive = true;
            this.consultationStartTime = new Date().toLocaleTimeString('fr-FR', {
                hour: '2-digit',
                minute: '2-digit'
            });
            
            // Basculer vers l'onglet consultations
            this.activeTab = 'consultations';
            
            console.log('Consultation démarrée à', this.consultationStartTime);
        },
        
        async endConsultation() {
            if (!this.isConsultationActive) return;
            
            try {
                // Préparer les données de la consultation
                const consultationDataToSave = {
                    patient_id: this.patientData.id,
                    consultation_date: new Date().toISOString().slice(0, 19).replace('T', ' '),
                    motif: this.consultationData.motif || '',
                    symptoms: this.consultationData.symptoms || '',
                    clinical_examination: this.consultationData.examenMedical || '',
                    etiopathic_diagnosis: this.consultationData.diagnostic || '',
                    treatment_plan: this.consultationData.treatmentPlan || '',
                    recommendations: this.consultationData.recommendations || '',
                    notes: this.consultationData.notes || '',
                    status: this.consultationData.status || 'normale',
                    price: 0,
                    payment_status: 'pending'
                };
                
                // Envoyer les données au serveur Laravel
                const response = await fetch(`/patients/${this.patientData.id}/consultations`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify(consultationDataToSave)
                });
                
                if (response.ok) {
                    const result = await response.json();
                    
                    // Arrêter la consultation
                    this.isConsultationActive = false;
                    this.consultationStartTime = '';
                    
                    // Mettre à jour les données de consultation courante
                    this.currentConsultationData = {
                        ...consultationDataToSave,
                        id: result.consultation_id || Date.now()
                    };
                    
                    // Ajouter à la liste des consultations
                    this.allConsultations.unshift(this.currentConsultationData);
                    
                    alert('Consultation enregistrée avec succès !');
                    console.log('Consultation sauvegardée:', result);
                } else {
                    throw new Error('Erreur serveur: ' + response.status);
                }
                
            } catch (error) {
                console.error('Erreur lors de la sauvegarde:', error);
                alert('Erreur lors de la sauvegarde de la consultation: ' + error.message);
            }
        },
        
        async savePatientData() {
            try {
                const patientDataToSave = {
                    first_name: this.patientData.firstName,
                    last_name: this.patientData.lastName,
                    phone: this.patientData.phone,
                    birthdate: this.patientData.birthdate,
                    email: this.patientData.email,
                    address: this.patientData.address
                };
                
                const response = await fetch(`/patients/${this.patientData.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify(patientDataToSave)
                });
                
                if (response.ok) {
                    console.log('Données patient mises à jour avec succès');
                } else {
                    throw new Error('Erreur serveur: ' + response.status);
                }
                
            } catch (error) {
                console.error('Erreur lors de la sauvegarde des données patient:', error);
                alert('Erreur lors de la sauvegarde des données du patient: ' + error.message);
            }
        },
        
        addAntecedent() {
            this.antecedents.push({
                title: '',
                description: ''
            });
        },
        
        removeAntecedent(index) {
            this.antecedents.splice(index, 1);
        },
        
        async addPatient() {
            try {
                const newPatientData = {
                    first_name: this.newPatient.firstName,
                    last_name: this.newPatient.lastName,
                    phone: this.newPatient.phone,
                    birthdate: this.newPatient.birthdate
                };
                
                const response = await fetch('/patients', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify(newPatientData)
                });
                
                if (response.ok) {
                    const result = await response.json();
                    
                    // Réinitialiser le formulaire
                    this.newPatient = {
                        firstName: '',
                        lastName: '',
                        phone: '',
                        birthdate: ''
                    };
                    
                    // Fermer le modal
                    this.showNewPatientForm = false;
                    
                    alert('Patient ajouté avec succès !');
                    
                    // Optionnel: rediriger vers le nouveau patient
                    if (result.patient_id) {
                        window.location.href = `/patients/${result.patient_id}`;
                    }
                } else {
                    throw new Error('Erreur serveur: ' + response.status);
                }
                
            } catch (error) {
                console.error('Erreur lors de la création du patient:', error);
                alert('Erreur lors de la création du patient: ' + error.message);
            }
        },
        
        // Auto-sauvegarde pendant la consultation
        autoSave() {
            if (this.isConsultationActive) {
                console.log('Auto-sauvegarde en cours...');
                // Ici vous pourriez implémenter une sauvegarde automatique des brouillons
            }
        },
        
        // Modal de paiement
        showPaymentModal: false,
        selectedConsultation: null,
        paymentData: {
            amount: 0,
            paymentMethod: '',
            isFree: false
        },
        
        openPaymentModal(consultation) {
            this.selectedConsultation = consultation;
            this.paymentData = {
                amount: consultation.price || 0,
                paymentMethod: '',
                isFree: false
            };
            this.showPaymentModal = true;
        },
        
        closePaymentModal() {
            this.showPaymentModal = false;
            this.selectedConsultation = null;
            this.paymentData = {
                amount: 0,
                paymentMethod: '',
                isFree: false
            };
        },
        
        async processPayment() {
            if (!this.selectedConsultation) return;
            
            try {
                const paymentInfo = {
                    consultation_id: this.selectedConsultation.id,
                    amount: this.paymentData.isFree ? 0 : this.paymentData.amount,
                    payment_method: this.paymentData.isFree ? 'gratuit' : this.paymentData.paymentMethod,
                    payment_status: 'paid',
                    payment_date: new Date().toISOString().slice(0, 19).replace('T', ' ')
                };
                
                const response = await fetch(`/consultations/${this.selectedConsultation.id}/mark-paid`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(paymentInfo)
                });
                
                if (response.ok) {
                    const result = await response.json();
                    
                    // Mettre à jour le statut localement
                    this.selectedConsultation.payment_status = 'paid';
                    this.selectedConsultation.price = paymentInfo.amount;
                    
                    // Fermer le modal
                    this.closePaymentModal();
                    
                    // Afficher un message de succès
                    alert('Paiement enregistré avec succès !');
                    
                    // Mettre à jour l'interface en rechargeant la page
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                    
                } else {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Erreur lors de l\'enregistrement du paiement');
                }
                
            } catch (error) {
                console.error('Erreur lors du traitement du paiement:', error);
                alert('Erreur lors du traitement du paiement: ' + error.message);
            }
        }
    }
}

// Auto-sauvegarde toutes les 30 secondes pendant une consultation
setInterval(() => {
    const app = document.querySelector('[x-data]');
    if (app && app._x_dataStack && app._x_dataStack[0].isConsultationActive) {
        app._x_dataStack[0].autoSave();
    }
}, 30000);

// Gestion de couleur par SVG avec persistance locale par patient
window.skeletonColor = function(patientId, svgName) {
    const key = `patient:${patientId}:skeleton:${svgName}:color`;
    const palette = ['#111827','#0ea5e9','#10b981','#f59e0b','#ef4444','#a855f7'];
    const saved = localStorage.getItem(key);
    let i = saved ? Math.max(0, palette.indexOf(saved)) : 0;
    if (i === -1) i = 0;
    const state = {
        palette,
        i,
        get color(){ return this.palette[this.i]; },
        next(){ this.i = (this.i + 1) % this.palette.length; localStorage.setItem(key, this.palette[this.i]); }
    };
    // Sauvegarde initiale si aucune valeur
    if (!saved) localStorage.setItem(key, state.color);
    return state;
}
</script>

<style>
/* Styles pour les champs éditables */
.editable-field {
    transition: all 0.2s ease;
    cursor: default;
}

.editable-field.edit-mode {
    cursor: text;
}

.editable-field.edit-mode:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Animation pour l'indicateur de consultation active */
@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Style pour les champs en mode consultation */
.consultation-field {
    background-color: #fef3c7;
    border: 2px solid #f59e0b;
}

.consultation-field:focus {
    background-color: #ffffff;
    border-color: #3b82f6;
}

/* Transitions pour l'expansion des consultations */
.max-h-0 {
    max-height: 0;
    overflow: hidden;
}

.max-h-none {
    max-height: none;
}

/* Couleur dynamique pour les SVG du squelette */
.skeleton-colorable {
    --sk-color: #111827;
    transition: transform 0.2s ease, filter 0.2s ease;
}
.skeleton-colorable:hover {
    filter: drop-shadow(0 2px 8px rgba(0,0,0,0.15));
}
.skeleton-colorable svg {
    width: 100%;
    height: auto;
}
.skeleton-colorable svg *,
.skeleton-colorable svg path,
.skeleton-colorable svg g {
    stroke: var(--sk-color) !important;
    fill: var(--sk-color) !important;
}

/* Gabarit taille squelette */
.skeleton-part {
    max-width: 320px;
    margin-left: auto;
    margin-right: auto;
}
@media (min-width: 768px) {
    .skeleton-part { max-width: 380px; }
}

/* Mise en page compacte de la figure */
.skeleton-figure { max-width: 420px; }
@media (min-width: 768px) { .skeleton-figure { max-width: 480px; } }
.skeleton-head svg { width: 36%; margin-left: auto; margin-right: auto; display: block; }
.skeleton-torso { position: relative; top: -2px; transform: scale(0.72); transform-origin: top center; left: -15px; }
.skeleton-torso svg { width: 65%; display: block; margin: 0 auto; }
.skeleton-arm { transform: scale(3); transform-origin: top center; }
.skeleton-arm svg { width: 100%; display: block; }
.skeleton-pelvis { position: relative; top: -90px; }
.skeleton-pelvis svg { width: 52%; display: block; margin: 0 auto; }
.skeleton-leg svg { width: 85%; display: block; }

/* Ligne bras/torse centrée et symétrique */
.skeleton-row {
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    align-items: start;
    justify-items: center;
    column-gap: 8px;
}
</style>

<!-- Modal de paiement -->
<div x-show="showPaymentModal" x-cloak
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md mx-4">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Enregistrer le paiement</h2>
            <button @click="closePaymentModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <div class="space-y-6">
            <!-- Consultation info -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="font-medium text-gray-800 mb-2">Consultation</h3>
                <p class="text-sm text-gray-600" x-text="selectedConsultation ? 'Séance du ' + formatDate(selectedConsultation.consultation_date) : ''"></p>
            </div>
            
            <!-- Option gratuit -->
            <div class="space-y-3">
                <label class="flex items-center space-x-3 cursor-pointer">
                    <input type="checkbox" x-model="paymentData.isFree" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-gray-700 font-medium">Acte gratuit (pas d'impact dans la comptabilité)</span>
                </label>
            </div>
            
            <!-- Tarifs pré-enregistrés -->
            <div x-show="!paymentData.isFree" class="space-y-3">
                <label class="block text-sm font-medium text-gray-700 mb-2">Sélectionner un tarif</label>
                <div class="grid grid-cols-2 gap-3">
                    <button @click="paymentData.amount = 35" 
                            :class="paymentData.amount === 35 ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="p-3 rounded-lg border transition-colors">
                        35€
                    </button>
                    <button @click="paymentData.amount = 40" 
                            :class="paymentData.amount === 40 ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="p-3 rounded-lg border transition-colors">
                        40€
                    </button>
                    <button @click="paymentData.amount = 45" 
                            :class="paymentData.amount === 45 ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="p-3 rounded-lg border transition-colors">
                        45€
                    </button>
                    <button @click="paymentData.amount = 50" 
                            :class="paymentData.amount === 50 ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="p-3 rounded-lg border transition-colors">
                        50€
                    </button>
                </div>
                
                <!-- Tarif personnalisé -->
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ou tarif personnalisé</label>
                    <input type="number" x-model="paymentData.amount" min="0" step="0.01"
                           class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Montant en euros">
                </div>
            </div>
            
            <!-- Méthode de paiement -->
            <div x-show="!paymentData.isFree" class="space-y-3">
                <label class="block text-sm font-medium text-gray-700 mb-2">Méthode de paiement</label>
                <div class="space-y-2">
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="radio" x-model="paymentData.paymentMethod" value="especes" class="text-blue-600 focus:ring-blue-500">
                        <span class="text-gray-700">Espèces</span>
                    </label>
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="radio" x-model="paymentData.paymentMethod" value="cheque" class="text-blue-600 focus:ring-blue-500">
                        <span class="text-gray-700">Chèque</span>
                    </label>
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="radio" x-model="paymentData.paymentMethod" value="paylib" class="text-blue-600 focus:ring-blue-500">
                        <span class="text-gray-700">PayLib</span>
                    </label>
                </div>
            </div>
            
            <!-- Résumé -->
            <div x-show="!paymentData.isFree" class="bg-blue-50 p-4 rounded-lg">
                <div class="flex justify-between items-center">
                    <span class="font-medium text-gray-700">Total à payer:</span>
                    <span class="text-xl font-bold text-blue-600" x-text="paymentData.amount + '€'"></span>
                </div>
                <div x-show="paymentData.paymentMethod" class="mt-2 text-sm text-gray-600">
                    <span x-text="'Méthode: ' + paymentData.paymentMethod"></span>
                </div>
            </div>
            
            <!-- Boutons d'action -->
            <div class="flex justify-end space-x-3 pt-4">
                <button @click="closePaymentModal()" 
                        class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                    Annuler
                </button>
                <button @click="processPayment()" 
                        :disabled="!paymentData.isFree && (!paymentData.amount || !paymentData.paymentMethod)"
                        :class="(!paymentData.isFree && (!paymentData.amount || !paymentData.paymentMethod)) ? 'bg-gray-300 cursor-not-allowed' : 'bg-green-500 hover:bg-green-600'"
                        class="px-4 py-2 text-white rounded-lg transition-colors">
                    Enregistrer le paiement
                </button>
            </div>
        </div>
    </div>
</div>

</body>
</html>