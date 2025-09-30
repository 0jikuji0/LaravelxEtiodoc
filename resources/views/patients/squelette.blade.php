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
            { name: 'Crâne', src: 'images/crane.png', style: 'top:0px; left:260px; width:90px; z-index:1;' },
            { name: 'Machoire', src: 'images/machoire.png', style: 'top:40px; left:262px; width:83px; z-index=-1;' },

            // Clavicules et épaules
            { name: 'Clavicule D', src: 'images/clavicule_d.png', style: 'top:130px; left:350px; width:110px;' },
            { name: 'Clavicule G', src: 'images/clavicule_g.png', style: 'top:130px; left:140px; width:110px;' },
            { name: 'Épaule D', src: 'images/epaule_d.png', style: 'top:155px; left:370px; width:80px;' },
            { name: 'Épaule G', src: 'images/epaule_g.png', style: 'top:147px; left:150px; width:80px;' },

            // Bras
            { name: 'Humérus D', src: 'images/humerus_d.png', style: 'top:170px; left:380px; width:120px;' },
            { name: 'Humérus G', src: 'images/humerus_g.png', style: 'top:180px; left:110px; width:110px;' },
            { name: 'Radius D', src: 'images/radius_d.png', style: 'top:345px; left:400px; width:75px;' },
            { name: 'Radius G', src: 'images/radius_g.png', style: 'top:350px; left:125px; width:80px;' },
            { name: 'Main D', src: 'images/main_d.png', style: 'top:455px; left:400px; width:100px;' },
            { name: 'Main G', src: 'images/main_g.png', style: 'top:475px; left:125px; width:65px;' },

            // Sternum & côtes
            { name: 'Sternum', src: 'images/sternum.png', style: 'top:170px; left:283px; width:45px;' },
            { name: 'Côtes D', src: 'images/cotes_d.png', style: 'top:170px; left:325px; width:70px;' },
            { name: 'Côtes G', src: 'images/cotes_g.png', style: 'top:177px; left:216px; width:70px;' },

            // Colonne cervicale (C1–C7)
            { name: 'C1', src: 'images/c1.png', style: 'top:246px; left:290px; width:30px;' },
            { name: 'C2', src: 'images/c2.png', style: 'top:255px; left:290px; width:30px;' },
            { name: 'C3', src: 'images/c3.png', style: 'top:265px; left:290px; width:30px;' },
            { name: 'C4', src: 'images/c4.png', style: 'top:275px; left:290px; width:30px;' },
            { name: 'C5', src: 'images/c5.png', style: 'top:285px; left:290px; width:30px;' },
            { name: 'C6', src: 'images/c6.png', style: 'top:292px; left:290px; width:30px;' },
            { name: 'C7', src: 'images/c7.png', style: 'top:300px; left:290px; width:30px;' },


            { name: 'T1', src: 'images/t1.png', style: 'top:315px; left:288px; width:35px;' },
            { name: 'T2', src: 'images/t2.png', style: 'top:327px; left:288px; width:35px;' },
            { name: 'T3', src: 'images/t3.png', style: 'top:337px; left:288px; width:35px;' },
            { name: 'T4', src: 'images/t4.png', style: 'top:348px; left:288px; width:35px;' },
            { name: 'T5', src: 'images/t5.png', style: 'top:358px; left:288px; width:35px;' },
            { name: 'T6', src: 'images/t6.png', style: 'top:372px; left:288px; width:35px;' },
            { name: 'T7', src: 'images/t7.png', style: 'top:382px; left:288px; width:35px;' },
            { name: 'T8', src: 'images/t8.png', style: 'top:396px; left:288px; width:35px;' },
            { name: 'T9', src: 'images/t9.png', style: 'top:408px; left:288px; width:35px;' },
            { name: 'T10', src: 'images/t10.png', style: 'top:422px; left:288px; width:35px;' },
            { name: 'T11', src: 'images/t11.png', style: 'top:434px; left:288px; width:35px;' },
            { name: 'T12', src: 'images/t12.png', style: 'top:448px; left:288px; width:35px;' },
/*
            // Colonne lombaire (L1–L5)
            { name: 'L1', src: 'images/l1.png', style: 'top:420px; left:280px; width:40px;' },
            { name: 'L2', src: 'images/l2.png', style: 'top:430px; left:280px; width:40px;' },
            { name: 'L3', src: 'images/l3.png', style: 'top:440px; left:280px; width:40px;' },
            { name: 'L4', src: 'images/l4.png', style: 'top:450px; left:280px; width:40px;' },
            { name: 'L5', src: 'images/l5.png', style: 'top:460px; left:280px; width:40px;' },
           */// Sacrum & bassin
            { name: 'Sacrum H', src: 'images/sacrum_h.png', style: 'top:540px; left:272px; width:70px;' },
            { name: 'Sacrum B', src: 'images/sacrum_b.png', style: 'top:609px; left:297px; width:20px;' },
            { name: 'Os oxal H D', src: 'images/os_oxal_h_d.png', style: 'top:545px; left:328px; width:52px;' },
            { name: 'Os oxal H G', src: 'images/os_oxal_h_g.png', style: 'top:545px; left:232px; width:54px;' },
            { name: 'Os oxal B 1 D', src: 'images/os_oxal_b_1_d.png', style: 'top:594px; left:323px; width:40px;' },
            { name: 'Os oxal B 1 G', src: 'images/os_oxal_b_1_g.png', style: 'top:593px; left:248px; width:40px;' },
            { name: 'Os oxal B 2 D', src: 'images/os_oxal_b_2_d.png', style: 'top:622px; left:306px; width:32px;' },
            { name: 'Os oxal B 2 G', src: 'images/os_oxal_b_2_g.png', style: 'top:622px; left:275px; width:30px;' },

            // Jambes
            { name: 'Fémur D', src: 'images/femur_d.png', style: 'top:650px; left:309px; width:130px;' },
            { name: 'Fémur G', src: 'images/femur_g.png', style: 'top:635px; left:184px; width:145px;' },
            { name: 'Genou D', src: 'images/genou_d.png', style: 'top:815px; left:358px; width:20px;' },
            { name: 'Genou G', src: 'images/genou_g.png', style: 'top:813px; left:222px; width:22px;' },
            { name: 'Tibia D', src: 'images/tibia_d.png', style: 'top:835px; left:297px; width:100px;' },
            { name: 'Tibia G', src: 'images/tibia_g.png', style: 'top:828px; left:209px; width:100px;' },
            { name: 'Pied D', src: 'images/pied_d.png', style: 'top:990px; left:319px; width:100px;' },
            { name: 'Pied G', src: 'images/pied_g.png', style: 'top:980px; left:169px; width:120px;' }
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
