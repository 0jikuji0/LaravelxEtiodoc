<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>MARTINS Lilo - Etiodoc</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
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
            <a href="#" class="text-gray-700 hover:text-blue-600">Comptabilité</a>
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
                <span class="mr-2">Lea</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="open" @click.away="open = false"
                 class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-2 z-50" style="display:none" x-cloak>
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    Mon compte
                </a>
                <button type="button"
                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                    Déconnexion
                </button>
            </div>
        </div>
    </div>
</header>

<!-- Contenu principal -->
<div class="px-6 py-8">
    <!-- En-tête patient -->
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">MARTINS Lilo</h1>
        <div class="flex space-x-3">
            <!-- Bouton Modifier général -->
            {{-- <button @click="toggleEditMode()" 
                    x-show="!isConsultationActive"
                    :class="editMode ? 'bg-green-500 hover:bg-green-600' : 'bg-gray-500 hover:bg-gray-600'"
                    class="text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          :d="editMode ? 'M5 13l4 4L19 7' : 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'" />
                </svg>
                <span x-text="editMode ? 'Sauvegarder' : 'Modifier'"></span>
            </button> --}}
            
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

        <!-- Consultations -->
        <div x-show="activeTab === 'consultations'" class="space-y-6">
            <!-- Consultation en cours ou récente -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800" 
                        x-text="isConsultationActive ? 'Consultation en cours' : 'Séance du 23 avril 2025 par VERDIERE Beatrice'"></h3>
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
                              x-text="isConsultationActive ? 'Consultation en cours' : 'Consultation normale'"></span>
                    </div>
                </div>

                <!-- Motif -->
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <h4 class="text-red-800 font-medium text-sm uppercase tracking-wide mb-3">Motif</h4>
                    <div x-show="!isConsultationActive" class="text-gray-700" 
                         x-text="consultationData.motif || 'Aucun motif enregistré'">
                    </div>
                    <div x-show="isConsultationActive" class="editable-field edit-mode">
                        <textarea x-ref="motif"
                                  x-model="consultationData.motif"
                                  rows="3"
                                  class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none bg-white"
                                  placeholder="Motif de la consultation..."
                        ></textarea>
                    </div>
                </div>

                <!-- Examen médical -->
                <div class="bg-gray-50 rounded-lg p-6 mb-6">
                    <h4 class="text-gray-600 font-medium text-center mb-6">Examen médical</h4>
                    
                    <div x-show="!isConsultationActive" class="text-gray-700 whitespace-pre-line" 
                         x-text="consultationData.examenMedical || 'Aucun examen enregistré'">
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
                <div class="bg-blue-500 text-white rounded-lg p-4">
                    <h3 class="font-medium mb-3">Diagnostic Étiopathique</h3>
                    <div x-show="!isConsultationActive" class="text-white" 
                         x-text="consultationData.diagnostic || 'Aucun diagnostic enregistré'">
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
            </div>
        </div>
        
        <!-- Comptes-rendus -->
        <div x-show="activeTab === 'comptes'" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-6">Comptes-rendus médicaux</h3>
            <div class="text-center py-8 text-gray-500">
                Aucun compte-rendu disponible
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
        
        // Données du patient
        patientData: {
            id: 1,
            firstName: 'Lilo',
            lastName: 'MARTINS',
            phone: '06.12.34.56.78',
            birthdate: '1995-08-15',
            email: 'lilo.martins@email.com',
            address: ''
        },
        
        // Données de consultation
        consultationData: {
            motif: 'Douleurs cervicales et vertiges',
            examenMedical: `Di cervicales:
A était poussé par l'arrière tête reste derrière
Vertige passager ce week-end

Après matchs tendue ++++++

Cheville droite tordue ce week-end

Revoir insertion tibiale antérieur`,
            diagnostic: 'Dysfonction cervicale haute avec répercussion vestibulaire'
        },
        
        // Consultation en cours
        currentConsultation: {
            motif: '',
            examenMedical: '',
            diagnostic: '',
            status: 'normale'
        },
        
        // Antécédents
        antecedents: [
            {
                title: 'Entorse cheville droite',
                description: 'Entorse récente suite activité sportive'
            },
            {
                title: 'Tensions cervicales récurrentes',
                description: 'Tensions musculaires fréquentes après activité sportive'
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
            
            // Initialiser les données de la consultation courante mais garder les anciennes données
            this.currentConsultation = {
                motif: this.consultationData.motif || '',
                examenMedical: this.consultationData.examenMedical || '',
                diagnostic: this.consultationData.diagnostic || '',
                status: 'normale'
            };
            
            // GARDER les données existantes - ne pas les vider
            // Les champs gardent leurs valeurs précédentes et on peut ajouter par-dessus
            
            console.log('Consultation démarrée à', this.consultationStartTime);
            console.log('Données conservées:', this.consultationData);
        },
        
        async endConsultation() {
            if (!this.isConsultationActive) return;
            
            try {
                // Préparer les données de la consultation
                const consultationDataToSave = {
                    patient_id: this.patientData.id,
                    user_id: 1, // ID de l'utilisateur connecté
                    consultation_date: new Date().toISOString().slice(0, 19).replace('T', ' '),
                    motif: this.consultationData.motif || '',
                    clinical_examination: this.consultationData.examenMedical || '',
                    etiopathic_diagnosis: this.consultationData.diagnostic || '',
                    status: this.currentConsultation.status || 'normale'
                };
                
                // Simuler l'appel API pour sauvegarder la consultation
                await this.saveConsultationToDB(consultationDataToSave);
                
                // Arrêter la consultation
                this.isConsultationActive = false;
                this.consultationStartTime = '';
                
                // Message de confirmation
                alert('Consultation enregistrée avec succès dans la base de données !');
                
                console.log('Consultation sauvegardée:', consultationDataToSave);
                
            } catch (error) {
                console.error('Erreur lors de la sauvegarde:', error);
                alert('Erreur lors de la sauvegarde de la consultation');
            }
        },
        
        async saveConsultationToDB(consultationData) {
            // Simulation d'un appel API
            // Dans un vrai projet, vous feriez un appel fetch vers votre backend
            return new Promise((resolve, reject) => {
                setTimeout(() => {
                    try {
                        // Simuler la sauvegarde en base de données
                        console.log('Sauvegarde en BDD:', {
                            table: 'consultations',
                            data: consultationData,
                            sql: `INSERT INTO consultations (patient_id, user_id, consultation_date, motif, clinical_examination, etiopathic_diagnosis, status) 
                                  VALUES (${consultationData.patient_id}, ${consultationData.user_id}, '${consultationData.consultation_date}', 
                                         '${consultationData.motif}', '${consultationData.clinical_examination}', 
                                         '${consultationData.etiopathic_diagnosis}', '${consultationData.status}')`
                        });
                        
                        // Simuler une réponse de succès
                        resolve({
                            success: true,
                            consultation_id: Math.floor(Math.random() * 1000) + 1,
                            message: 'Consultation sauvegardée avec succès'
                        });
                    } catch (error) {
                        reject(error);
                    }
                }, 1000); // Simuler le délai réseau
            });
        },
        
        async savePatientData() {
            try {
                // Préparer les données du patient à sauvegarder
                const patientDataToSave = {
                    id: this.patientData.id,
                    first_name: this.patientData.firstName,
                    last_name: this.patientData.lastName,
                    phone: this.patientData.phone,
                    birthdate: this.patientData.birthdate,
                    email: this.patientData.email,
                    address: this.patientData.address
                };
                
                // Simuler l'appel API pour mettre à jour le patient
                await this.savePatientToDB(patientDataToSave);
                
                console.log('Données patient sauvegardées:', patientDataToSave);
                
            } catch (error) {
                console.error('Erreur lors de la sauvegarde des données patient:', error);
                alert('Erreur lors de la sauvegarde des données du patient');
            }
        },
        
        async savePatientToDB(patientData) {
            // Simulation d'un appel API pour la mise à jour du patient
            return new Promise((resolve, reject) => {
                setTimeout(() => {
                    try {
                        console.log('Mise à jour patient en BDD:', {
                            table: 'patients',
                            data: patientData,
                            sql: `UPDATE patients SET 
                                    first_name = '${patientData.first_name}',
                                    last_name = '${patientData.last_name}',
                                    phone = '${patientData.phone}',
                                    birthdate = '${patientData.birthdate}',
                                    email = '${patientData.email}',
                                    address = '${patientData.address}',
                                    updated_at = NOW()
                                  WHERE id = ${patientData.id}`
                        });
                        
                        resolve({
                            success: true,
                            message: 'Patient mis à jour avec succès'
                        });
                    } catch (error) {
                        reject(error);
                    }
                }, 500);
            });
        },
        
        async saveAntecedentsToDB() {
            try {
                // Sauvegarder chaque antécédent
                for (const antecedent of this.antecedents) {
                    const antecedentData = {
                        patient_id: this.patientData.id,
                        category: 'medical',
                        title: antecedent.title,
                        description: antecedent.description,
                        is_active: true
                    };
                    
                    console.log('Sauvegarde antécédent en BDD:', {
                        table: 'medical_history',
                        data: antecedentData,
                        sql: `INSERT INTO medical_history (patient_id, category, title, description, is_active) 
                              VALUES (${antecedentData.patient_id}, '${antecedentData.category}', 
                                     '${antecedentData.title}', '${antecedentData.description}', ${antecedentData.is_active})
                              ON DUPLICATE KEY UPDATE 
                              title = VALUES(title), 
                              description = VALUES(description),
                              updated_at = NOW()`
                    });
                }
            } catch (error) {
                console.error('Erreur lors de la sauvegarde des antécédents:', error);
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
            // Si en consultation active, sauvegarder immédiatement
            if (this.isConsultationActive) {
                this.saveAntecedentsToDB();
            }
        },
        
        addPatient() {
            // Ici vous pourriez ajouter la logique pour créer un nouveau patient
            console.log('Nouveau patient:', this.newPatient);
            
            // Simulation de la création en BDD
            const newPatientData = {
                user_id: 1, // ID de l'utilisateur connecté
                first_name: this.newPatient.firstName,
                last_name: this.newPatient.lastName,
                phone: this.newPatient.phone,
                birthdate: this.newPatient.birthdate
            };
            
            console.log('Création patient en BDD:', {
                table: 'patients',
                data: newPatientData,
                sql: `INSERT INTO patients (user_id, first_name, last_name, phone, birthdate) 
                      VALUES (${newPatientData.user_id}, '${newPatientData.first_name}', 
                             '${newPatientData.last_name}', '${newPatientData.phone}', 
                             '${newPatientData.birthdate}')`
            });
            
            // Réinitialiser le formulaire
            this.newPatient = {
                firstName: '',
                lastName: '',
                phone: '',
                birthdate: ''
            };
            
            // Fermer le modal
            this.showNewPatientForm = false;
            
            // Afficher un message de succès
            alert('Patient ajouté avec succès !');
        },
        
        // Méthodes d'auto-sauvegarde pendant la consultation
        autoSave() {
            if (this.isConsultationActive) {
                // Sauvegarder automatiquement les modifications pendant la consultation
                console.log('Auto-sauvegarde en cours...');
                
                // Sauvegarder les antécédents modifiés
                this.saveAntecedentsToDB();
                
                // Vous pourriez aussi sauvegarder un brouillon de la consultation
                const draftData = {
                    patient_id: this.patientData.id,
                    motif: this.consultationData.motif,
                    clinical_examination: this.consultationData.examenMedical,
                    etiopathic_diagnosis: this.consultationData.diagnostic,
                    is_draft: true,
                    last_updated: new Date().toISOString()
                };
                
                // Stocker temporairement en mémoire (localStorage n'est pas disponible ici)
                console.log('Brouillon sauvegardé:', draftData);
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
</style>

</body>
</html>