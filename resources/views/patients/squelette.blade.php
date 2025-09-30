<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Squelette interactif</title>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        body {
            font-family: sans-serif;
            text-align: center;
            background-color: #f5f5f5;
        }

        h1 {
            margin-top: 30px;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            gap: 30px;
            margin-top: 50px;
        }

        .skeleton-container {
            position: relative;
            width: 600px;
            height: 1200px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        .skeleton-container img {
            position: absolute;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .selected-os {
            font-weight: bold;
            color: #333;
            text-align: left;
            min-width: 200px;
        }
    </style>
</head>
<body>
    <h1>Squelette interactif</h1>

    <div class="container" x-data="skeletonInteractive()">
        <!-- Squelette -->
        <div class="skeleton-container">
            <template x-for="os in osList" :key="os.name">
                <img 
                    :src="os.src" 
                    :alt="os.name" 
                    :style="os.style + (selectedOs === os.name ? '; filter: drop-shadow(0 0 15px ' + getColor(os) + ')' : '')"
                    :class="{'active': selectedOs === os.name}"
                    @click="selectOs(os)">
            </template>
        </div>

        <!-- Texte à droite -->
        <div class="selected-os" 
             x-text="selectedOs ? 'Os sélectionné : ' + selectedOs : 'Cliquez sur un os'">
        </div>
    </div>

    <script>
    function skeletonInteractive() {
        return {
            selectedOs: null,
            osList: [
                { name: 'Crâne', src: '{{ asset("images/crane.png") }}', style: 'top:0px; left:260px; width:90px;' },
                { name: 'Machoire', src: '{{ asset("images/machoire.png") }}', style: 'top:77px; left:276px; width:65px;' },
                { name: 'Clavicule D', src: '{{ asset("images/clavicule_d.png") }}', style: 'top:130px; left:350px; width:110px;' },
                { name: 'Clavicule G', src: '{{ asset("images/clavicule_g.png") }}', style: 'top:130px; left:140px; width:110px;' },
                { name: 'Épaule D', src: '{{ asset("images/epaule_d.png") }}', style: 'top:155px; left:370px; width:80px;' },
                { name: 'Épaule G', src: '{{ asset("images/epaule_g.png") }}', style: 'top:147px; left:150px; width:80px;' },
                { name: 'Humérus D', src: '{{ asset("images/humerus_d.png") }}', style: 'top:170px; left:380px; width:120px;' },
                { name: 'Humérus G', src: '{{ asset("images/humerus_g.png") }}', style: 'top:180px; left:110px; width:110px;' },
                { name: 'Radius D', src: '{{ asset("images/radius_d.png") }}', style: 'top:345px; left:400px; width:75px;' },
                { name: 'Radius G', src: '{{ asset("images/radius_g.png") }}', style: 'top:350px; left:125px; width:80px;' },
                { name: 'Main D', src: '{{ asset("images/main_d.png") }}', style: 'top:455px; left:400px; width:100px;' },
                { name: 'Main G', src: '{{ asset("images/main_g.png") }}', style: 'top:475px; left:125px; width:65px;' },
                { name: 'Sternum', src: '{{ asset("images/sternum.png") }}', style: 'top:170px; left:275px; width:65px;' },
                { name: 'Côtes D', src: '{{ asset("images/cotes_d.png") }}', style: 'top:170px; left:325px; width:70px;' },
                { name: 'Côtes G', src: '{{ asset("images/cotes_g.png") }}', style: 'top:177px; left:216px; width:70px;' },
                { name: 'Colonne 1', src: '{{ asset("images/colonne_1.png") }}', style: 'top:290px; left:266px; width:80px;' },
                { name: 'Colonne 2', src: '{{ asset("images/colonne_2.png") }}', style: 'top:350px; left:229px; width:160px;' },
                { name: 'Colonne 3', src: '{{ asset("images/colonne_3.png") }}', style: 'top:480px; left:273px; width:75px;' },
                { name: 'Sacrum H', src: '{{ asset("images/sacrum_h.png") }}', style: 'top:540px; left:272px; width:70px;' },
                { name: 'Sacrum B', src: '{{ asset("images/sacrum_b.png") }}', style: 'top:609px; left:297px; width:20px;' },
                { name: 'Os oxal H D', src: '{{ asset("images/os_oxal_h_d.png") }}', style: 'top:545px; left:328px; width:52px;' },
                { name: 'Os oxal H G', src: '{{ asset("images/os_oxal_h_g.png") }}', style: 'top:545px; left:232px; width:54px;' },
                { name: 'Os oxal B 1 D', src: '{{ asset("images/os_oxal_b_1_d.png") }}', style: 'top:594px; left:323px; width:40px;' },
                { name: 'Os oxal B 1 G', src: '{{ asset("images/os_oxal_b_1_g.png") }}', style: 'top:593px; left:248px; width:40px;' },
                { name: 'Os oxal B 2 D', src: '{{ asset("images/os_oxal_b_2_d.png") }}', style: 'top:622px; left:306px; width:32px;' },
                { name: 'Os oxal B 2 G', src: '{{ asset("images/os_oxal_b_2_g.png") }}', style: 'top:622px; left:275px; width:30px;' },
                { name: 'Fémur D', src: '{{ asset("images/femur_d.png") }}', style: 'top:650px; left:309px; width:130px;' },
                { name: 'Fémur G', src: '{{ asset("images/femur_g.png") }}', style: 'top:635px; left:184px; width:145px;' },
                { name: 'Genou D', src: '{{ asset("images/genou_d.png") }}', style: 'top:815px; left:358px; width:20px;' },
                { name: 'Genou G', src: '{{ asset("images/genou_g.png") }}', style: 'top:813px; left:222px; width:22px;' },
                { name: 'Tibia D', src: '{{ asset("images/tibia_d.png") }}', style: 'top:835px; left:297px; width:100px;' },
                { name: 'Tibia G', src: '{{ asset("images/tibia_g.png") }}', style: 'top:828px; left:209px; width:100px;' },
                { name: 'Pied D', src: '{{ asset("images/pied_d.png") }}', style: 'top:990px; left:319px; width:100px;' },
                { name: 'Pied G', src: '{{ asset("images/pied_g.png") }}', style: 'top:980px; left:169px; width:120px;' }
            ],

            // Sélection/désélection
            selectOs(os) {
                this.selectedOs = (this.selectedOs === os.name) ? null : os.name;
            },

            // Couleur selon la position verticale
            getColor(os) {
                const top = parseInt(os.style.match(/top:(\d+)px/)[1]);
                if (top < 100) return 'red';       // tête
                if (top < 200) return 'yellow';     // clavicules / épaules
                if (top < 500) return 'orange';      // thorax / colonne
                if (top < 650) return 'green';     // bassin
                if (top < 800) return 'blue';        // jambes
                return 'purple';                      // pieds
            }
        }
    }
    </script>
</body>
</html>
