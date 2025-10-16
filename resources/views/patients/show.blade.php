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
@include('layouts.topbar')

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
                                                @click="openPaymentModal(consultation.id, consultation.price)"
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

                <!-- Examen médical -->
                <div class="bg-gray-50 rounded-lg p-6 mb-6">
                    <h4 class="text-gray-600 font-medium text-center mb-6">Examen médical</h4>
                    
                    <div x-show="!isConsultationActive" class="text-gray-700 whitespace-pre-line" 
                         x-text="currentConsultationData.clinical_examination || 'Aucun examen enregistré'">
                    </div>
                    <div x-show="isConsultationActive" class="editable-field edit-mode">
                        <textarea x-ref="examenMedical"
                                  x-model="consultationData.examenMedical"
                                  rows="8"
                                  class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none bg-white"
                                  placeholder="Détails de l'examen médical..."
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
                <div class="flex justify-center items-start mt-10">
    @include('patients.squelette')
</div>

<div class="grid grid-cols-1 gap-4">
    <div class="bg-gray-50 rounded-lg border border-gray-200 p-3 overflow-hidden">
        <h5 class="text-gray-700 font-medium mb-3">Vue de face</h5>

        <div class="skeleton-figure mx-auto relative" x-data="skeletonInteractive()">

            <!-- Crâne -->
            <template x-for="os in parts.crane" :key="os.name">
                <img :src="os.src" :alt="os.name"
                     @click="selectOs(os)"
                     class="absolute cursor-pointer transition-all duration-200"
                     :class="{'opacity-50': selectedOs === os.name}"
                     :style="os.style">
            </template>

            <!-- Machoire -->
            <template x-for="os in parts.machoire" :key="os.name">
                <img :src="os.src" :alt="os.name"
                     @click="selectOs(os)"
                     class="absolute cursor-pointer transition-all duration-200"
                     :class="{'opacity-50': selectedOs === os.name}"
                     :style="os.style">
            </template>

            <!-- Clavicule -->
            <template x-for="os in parts.clavicule" :key="os.name">
                <img :src="os.src" :alt="os.name"
                     @click="selectOs(os)"
                     class="absolute cursor-pointer transition-all duration-200"
                     :class="{'opacity-50': selectedOs === os.name}"
                     :style="os.style">
            </template>

            <!-- Épaules -->
            <template x-for="os in parts.epaule" :key="os.name">
                <img :src="os.src" :alt="os.name"
                     @click="selectOs(os)"
                     class="absolute cursor-pointer transition-all duration-200"
                     :class="{'opacity-50': selectedOs === os.name}"
                     :style="os.style">
            </template>

            <!-- Bras / Humérus / Radius / Main -->
            <template x-for="os in parts.bras" :key="os.name">
                <img :src="os.src" :alt="os.name"
                     @click="selectOs(os)"
                     class="absolute cursor-pointer transition-all duration-200"
                     :class="{'opacity-50': selectedOs === os.name}"
                     :style="os.style">
            </template>

            <!-- Colonne -->
            <template x-for="os in parts.colonne" :key="os.name">
                <img :src="os.src" :alt="os.name"
                     @click="selectOs(os)"
                     class="absolute cursor-pointer transition-all duration-200"
                     :class="{'opacity-50': selectedOs === os.name}"
                     :style="os.style">
            </template>

            <!-- Côtes / Sternum -->
            <template x-for="os in parts.cotes" :key="os.name">
                <img :src="os.src" :alt="os.name"
                     @click="selectOs(os)"
                     class="absolute cursor-pointer transition-all duration-200"
                     :class="{'opacity-50': selectedOs === os.name}"
                     :style="os.style">
            </template>

            <!-- Bassin / Os oxal / Sacrum -->
            <template x-for="os in parts.bassin" :key="os.name">
                <img :src="os.src" :alt="os.name"
                     @click="selectOs(os)"
                     class="absolute cursor-pointer transition-all duration-200"
                     :class="{'opacity-50': selectedOs === os.name}"
                     :style="os.style">
            </template>

            <!-- Jambes / Tibia / Fémur / Genou / Pieds -->
            <template x-for="os in parts.jambes" :key="os.name">
                <img :src="os.src" :alt="os.name"
                     @click="selectOs(os)"
                     class="absolute cursor-pointer transition-all duration-200"
                     :class="{'opacity-50': selectedOs === os.name}"
                     :style="os.style">
            </template>

            <!-- Nom de l'os sélectionné -->
            <div class="mt-3 text-center text-gray-700 font-medium absolute bottom-0 w-full" x-text="selectedOs ? 'Os sélectionné : ' + selectedOs : 'Cliquez sur un os pour voir son nom'"></div>

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

<!-- Modal Paiement consultation -->
<div x-show="showPaymentModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div @click.away="showPaymentModal = false" class="bg-white w-full max-w-lg rounded-lg shadow-xl">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-800">Valider le paiement</h2>
            <button @click="showPaymentModal = false" class="text-gray-500 hover:text-gray-700">✕</button>
        </div>
        <div class="p-6 space-y-5">
            <!-- Acte gratuit -->
            <div class="flex items-center gap-3">
                <input id="freeAct" type="checkbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded" x-model="paymentForm.isFree">
                <label for="freeAct" class="text-gray-800">Acte gratuit (n'impacte pas la comptabilité)</label>
            </div>

            <!-- Tarifs prédéfinis -->
            <div :class="paymentForm.isFree ? 'opacity-50 pointer-events-none' : ''">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tarif</label>
                <div class="flex flex-wrap gap-2">
                    <template x-for="t in [35,40,45,50]" :key="'tarif-'+t">
                        <button type="button"
                                @click="paymentForm.amount = t"
                                :class="paymentForm.amount === t ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-800 hover:bg-gray-200'"
                                class="px-3 py-2 rounded-md text-sm">
                            <span x-text="t + '€'"></span>
                        </button>
                    </template>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-500">ou</span>
                        <input type="number" min="0" step="5" x-model.number="paymentForm.amount" class="w-24 border border-gray-300 rounded px-2 py-2 text-sm" :disabled="paymentForm.isFree">
                        <span class="text-gray-600 text-sm">€</span>
                    </div>
                </div>
            </div>

            <!-- Moyen de paiement -->
            <div :class="paymentForm.isFree ? 'opacity-50 pointer-events-none' : ''">
                <label class="block text-sm font-medium text-gray-700 mb-2">Moyen de paiement</label>
                <div class="grid grid-cols-3 gap-3">
                    <label class="flex items-center gap-2 border rounded-md px-3 py-2 cursor-pointer hover:bg-gray-50"
                           :class="paymentForm.method === 'cheque' ? 'border-blue-500 ring-2 ring-blue-100' : 'border-gray-300'">
                        <input type="radio" name="paymethod" value="cheque" class="hidden" x-model="paymentForm.method">
                        <span>Chèque</span>
                    </label>
                    <label class="flex items-center gap-2 border rounded-md px-3 py-2 cursor-pointer hover:bg-gray-50"
                           :class="paymentForm.method === 'cash' ? 'border-blue-500 ring-2 ring-blue-100' : 'border-gray-300'">
                        <input type="radio" name="paymethod" value="cash" class="hidden" x-model="paymentForm.method">
                        <span>Espèces</span>
                    </label>
                    <label class="flex items-center gap-2 border rounded-md px-3 py-2 cursor-pointer hover:bg-gray-50"
                           :class="paymentForm.method === 'paylib' ? 'border-blue-500 ring-2 ring-blue-100' : 'border-gray-300'">
                        <input type="radio" name="paymethod" value="paylib" class="hidden" x-model="paymentForm.method">
                        <span>Paylib</span>
                    </label>
                </div>
            </div>

            <!-- Récap -->
            <div class="bg-gray-50 border border-gray-200 rounded-md p-3 text-sm text-gray-700">
                <div class="flex justify-between"><span>Montant</span><span x-text="paymentForm.isFree ? '0 €' : (paymentForm.amount || 0) + ' €'"></span></div>
                <div class="flex justify-between"><span>Paiement</span><span x-text="paymentForm.isFree ? '-' : (paymentForm.method === 'cash' ? 'Espèces' : (paymentForm.method === 'cheque' ? 'Chèque' : 'Paylib'))"></span></div>
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2">
            <button @click="showPaymentModal = false" class="px-4 py-2 rounded-md bg-gray-100 text-gray-800 hover:bg-gray-200">Annuler</button>
            <button @click="confirmPayment()" class="px-4 py-2 rounded-md bg-green-600 text-white hover:bg-green-700">Confirmer</button>
        </div>
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
        // Paiement modal
        showPaymentModal: false,
        paymentForm: {
            consultationId: null,
            amount: 45,
            isFree: false,
            method: 'cheque', // 'cheque' | 'cash' | 'paylib'
        },
        
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

        openPaymentModal(id, currentPrice) {
            this.paymentForm.consultationId = id;
            this.paymentForm.isFree = (currentPrice || 0) === 0;
            this.paymentForm.amount = currentPrice && currentPrice > 0 ? currentPrice : 45;
            this.paymentForm.method = 'cheque';
            this.showPaymentModal = true;
        },

        async confirmPayment() {
            try {
                const amount = this.paymentForm.isFree ? 0 : Number(this.paymentForm.amount || 0);
                const method = this.paymentForm.isFree ? null : this.paymentForm.method;
                const consultationId = this.paymentForm.consultationId;

                // 1) Créer la facture uniquement si montant > 0 (acte payant)
                if (amount > 0) {
                    const invoiceRes = await fetch('/accounting', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            patient_id: this.patientData.id,
                            amount: amount,
                            status: 'paid',
                            payment_method: method,
                            consultation_id: consultationId,
                            notes: `Paiement consultation #${consultationId}`
                        })
                    });
                    if (!invoiceRes.ok) {
                        const txt = await invoiceRes.text();
                        throw new Error('Création facture échouée: ' + txt);
                    }
                }

                // 2) Mettre à jour la consultation (prix + statut payé)
                const updateRes = await fetch(`/consultations/${consultationId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        price: amount,
                        payment_status: 'paid'
                    })
                });
                if (!updateRes.ok) {
                    const txt2 = await updateRes.text();
                    throw new Error('Mise à jour consultation échouée: ' + txt2);
                }

                // Mettre à jour en local
                const c = this.allConsultations.find(c => c.id === consultationId);
                if (c) { c.payment_status = 'paid'; c.price = amount; }

                this.showPaymentModal = false;
            } catch (e) {
                console.error(e);
                alert('Erreur lors de la validation du paiement');
            }
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
</style>

</body>
</html>