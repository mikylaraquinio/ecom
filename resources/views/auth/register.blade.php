@php($title = 'Register | FarmSmart')

<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background-color: #f5f8f3;
        }

        .register-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 120px);
            padding: 2rem;
        }

        .register-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            padding: 3rem 2.5rem;
            width: 100%;
            max-width: 480px;
            text-align: center;
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .register-card img {
            height: 65px;
            margin-bottom: 1rem;
        }

        .register-card h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2f4f1c;
            margin-bottom: 0.5rem;
        }

        .register-card p.subtitle {
            font-size: 0.95rem;
            color: #6b7280;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .input-group {
            text-align: left;
            margin-bottom: 1.25rem;
        }

        .input-group label {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.3rem;
        }

        .input-group input,
        .input-group textarea {
            width: 100%;
            padding: 0.75rem;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background-color: #f9fafb;
            font-size: 0.95rem;
            transition: border 0.2s ease, box-shadow 0.2s ease;
            resize: none;
        }

        .input-group input:focus,
        .input-group textarea:focus {
            border-color: #71b127;
            background-color: #fff;
            outline: none;
            box-shadow: 0 0 0 3px rgba(113, 177, 39, 0.1);
        }

        /* Register button */
        .farm-btn {
            background: linear-gradient(90deg, #71b127, #9feb47);
            color: white;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            padding: 0.85rem;
            font-size: 1rem;
            width: 100%;
            margin-top: 1rem;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.1s ease;
        }

        .farm-btn:hover {
            background: #5a9216;
            transform: translateY(-1px);
        }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.8rem 0;
            color: #9ca3af;
            font-size: 0.9rem;
        }

        .divider::before,
        .divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: #e5e7eb;
            margin: 0 0.75rem;
        }

        /* Google register button */
        .btn-google {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            background-color: #fff;
            border: 1px solid #dadce0;
            border-radius: 8px;
            color: #3c4043;
            font-size: 14px;
            font-weight: 500;
            font-family: 'Roboto', Arial, sans-serif;
            height: 45px;
            width: 100%;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
        }

        .btn-google img {
            width: 18px;
            height: 18px;
            vertical-align: middle;
            margin-top: -1px;
        }

        .btn-google:hover {
            background-color: #f7f8f8;
            border-color: #c6c6c6;
        }

        .btn-google:active {
            background-color: #e8e8e8;
            border-color: #a8a8a8;
        }

        /* Login link */
        .signup-text {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.95rem;
        }

        .signup-text a {
            color: #4C7737;
            font-weight: 600;
            text-decoration: none;
        }

        .signup-text a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .register-card {
                padding: 2rem 1.5rem;
            }
        }
    </style>

    <div class="register-container">
        <div class="register-card">
            <img src="{{ asset('assets/logo.png') }}" alt="FarmSmart Logo">
            <h2>Create Account</h2>
            <p class="subtitle">
                Join the FarmSmart community and grow your farm with powerful tools and connections.
            </p>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Full Name -->
                <div class="input-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name"  maxlength="50" minlength="3" placeholder="e.g. Juan Dela Cruz" value="{{ old('name') }}" required autofocus>
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>

                <!-- Email -->
                <div class="input-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email"  maxlength="100" placeholder="e.g. juan@email.com" value="{{ old('email') }}" required>
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <!-- Phone -->
                <div class="input-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" maxlength="11" placeholder="09XXXXXXXXX" value="{{ old('phone') }}" required pattern="^09\d{9}$">
                    <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                </div>

                <!-- Address -->
                <!-- Municipality / City -->
                <div class="input-group">
                    <label for="town">Municipality / City (Pangasinan)</label>
                    <select id="town" name="town" required>
                        <option value="">Select Municipality / City</option>
                    </select>
                    <x-input-error :messages="$errors->get('town')" class="mt-1" />
                </div>

                <!-- Barangay -->
                <div class="input-group">
                    <label for="barangay">Barangay</label>
                    <select id="barangay" name="barangay" required>
                        <option value="">Select Barangay</option>
                    </select>
                    <x-input-error :messages="$errors->get('barangay')" class="mt-1" />
                </div>

                <div class="input-group">
                    <label for="street">Street / House No. (Optional)</label>
                    <input type="text" id="street" name="street" maxlength="100" placeholder="e.g. Purok 2, Zone 5" value="{{ old('street') }}">
                    <x-input-error :messages="$errors->get('street')" class="mt-1" />
                </div>

                <!-- Hidden field for Pangasinan -->
                <input type="hidden" name="province" value="Pangasinan">

                <!-- Password -->
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" maxlength="100" required>
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                <!-- Confirm Password -->
                <div class="input-group">
                    <label for="password_confirmation">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" maxlength="100" required>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                </div>

                <button type="submit" class="farm-btn">Register</button>

                <div class="divider">or</div>

                <!-- Register with Google -->
                <button type="button" class="btn-google" onclick="window.location.href='{{ route('google.redirect') }}'">
                    <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google icon">
                    Register with Google
                </button>


                <div class="signup-text">
                    Already have an account? <a href="{{ route('login') }}">Log in</a>
                </div>
            </form>
        </div>
    </div>

    <small id="nameCounter" class="text-muted">0/50</small>
    <script>
        const nameInput = document.getElementById('name');
        const nameCounter = document.getElementById('nameCounter');
        nameInput.addEventListener('input', () => {
            nameCounter.textContent = `${nameInput.value.length}/50`;
        });
    </script>

    <script>
        document.getElementById('phone').addEventListener('input', function(e) {
        this.value = this.value.replace(/\D/g, ''); // remove non-numbers
        });
    </script>

    <script>
        const pangasinanData = {
                'Alaminos City': { barangays:['Alos','Amandiego', 'Arawan', 'Bail', 'Balogo', 'Banban', 'Balangobong', 'Basen', 'Bawas', 'Baybay', 'Bula', 'Cabungcalan', 'Caburque', 'Cagat', 'Calabanga', 'Calantipayan', 'Caranuan', 'Dapdap', 'Dimalawa', 'Don Don', 'In Salinas', 'Landas', 'Lima', 'Linmansangan', 'Lone', 'Lubong', 'Lucap', 'Magsaysay', 'Malig', 'Matag', 'Palamis', 'Pandac', 'Pangapian', 'Pao', 'Paraoir', 'Patbo', 'Payapay', 'Poblacion', 'Pudoc', 'Quinot', 'Sabangan', 'Sabangan East', 'Sabangan West', 'San Jose', 'San Miguel', 'Santa Rita', 'Saoang', 'Sayak', 'Tabor', 'Tampac', 'Tangcaran', 'Telbang', 'Tepat', 'Tikas', 'Tindog', 'Turong', 'Victoria'] },
                'Dagupan City': { barangays:['Bacayao Norte', 'Bacayao Sur', 'Barangay I', 'Barangay II', 'Barangay IV', 'Bolosan', 'Bonuan Binloc', 'Bonuan Boquig', 'Bonuan Gueset', 'Calmay', 'Carael', 'Caranglaan', 'Herrero', 'La Sip Chico', 'La Sip', 'Grande', 'Lomboy', 'Lucao', 'Malued', 'Mama Lingling', 'Mangin', 'Mayombo', 'Pantal', 'Poblacion Oeste', 'Barangay I', 'Pogo Chico', 'Pogo Grande', 'Pugaro Suit', 'Salapingao', 'Salisay', 'Tambac', 'Tapuac', 'Tebeng']},
                'San Carlos City': { barangays: ['Abanon','Agdao','Anciano T. Tandoc','Balite Sur','Balite Norte','Balingueo','Bayanihan','Bogaoan','Bolingit','Caoayan Kiling','Cobol','Coliling','Cruz','Dipalo','Guelew','Ilang','Inerangan','Libertad','Lilimbo','Longos','Lucban Paoay','Mabalbalino','Mancagayca','Matagdem','Mitolong','Narvacan East','Narvacan West','Palaris','Palaming','Pandayan','Pangalangan','Pangel','Paitan-Panoypoy','Parayao','Payapa','Payar','Poblacion East','Poblacion West','Roxas','Salinap','San Juan','San Pedro Taloy','Sapang','Sawang','Talang','Tamayan','Tandang Sora','Tigui','Turac'] },
                'Urdaneta City': { barangays: ['Anonas','Bactao','Bayaoas','Bolaoen','Cabaruan','Cabuloan','Calegu','Camantiles','Casantaan','Catablan','Cayambanan','Consuelo','Dilan Paurido','Labit Proper','Labit West','Maabay','Macalong','Nancalobasaan','Nangapugan','Palina East','Palina West','Poblacion','San Jose','San Vicente','Santa Lucia','Santo Domingo','Sugcong','Tipuso',] },
                'Agno': { barangays: ['Allabon', 'Alumina', 'Bayan East', 'Bayan West', 'Bega', 'Boboy', 'Dagupan', 'Gayusan', 'Guisay', 'Macaboboni', 'Poblacion East', 'Poblacion West', 'San Juan', 'Tupa'] },
                'Aguilar': { barangays: ['Bacante', 'Bale', 'Bawer', 'Baybay', 'Bita', 'Bongar', 'Cabayaoasan', 'Caguray', 'Calao', 'Calumbaya', 'Carmen East', 'Carmen West', 'Dapdappig', 'Dapla', 'Fisac', 'Gahard', 'Gandam', 'Guinbayan', 'Las-ud', 'Licsi', 'Liliao', 'Lubing', 'Mabini', 'Macabato', 'Malamin', 'Malupa', 'Nagsingcaoan', 'Naguelguel', 'Nangalisan', 'Poblacion', 'Pudoc', 'Pugo', 'Quimmarayan', 'Rang-ay', 'Sabangan', 'San Antonio', 'San Jose', 'Santa Cruz', 'Santa Maria', 'Sao Miguel', 'Sayak', 'Talelet', 'Tampac', 'Tao', 'Tondol', 'Tungao'] },
                'Alcala': { barangays: ['Anulid', 'Apalong', 'Bacquigue', 'Bagong Anac', 'Balingueo', 'Baybay Lopez', 'Baybay Polong', 'Botao', 'Buenavista', 'Bulaoen East', 'Bulaoen West', 'Cabcaburao', 'Calaocan East', 'Calaocan West', 'Carmen East', 'Carmen West', 'Dipalo', 'Guitna', 'Kisikis', 'Laoac East', 'Laoac West', 'Macayo', 'Pagbangkeruan', 'Paregu-eg East', 'Paregu-eg West', 'Poblacion East', 'Poblacion West', 'San Juan East', 'San Juan West', 'San Nicolas East', 'San Nicolas West', 'San Pedro Apartado', 'San Pedro IlI', 'San Vicente', 'Vacante'] },
                'Anda': { barangays: ['Awile', 'Bila', 'Boglai', 'Dolaoan', 'Macaleeng', 'Macapandan', 'Mal-ong', 'Manaoag', 'Poblacion', 'Sablig', 'San Jose', 'Siapar', 'Talogtog', 'Tandoc'] },
                'Asingan': { barangays: ['Arboleda', 'Alog', 'Ambonao', 'Angayan Norte', 'Angayan Sur', 'Antongalon', 'Apotol', 'Baay', 'Baguileo', 'Balandra', ' Bantog ', 'Baro', 'Bobonan', 'Cabaruan', 'Calepaan', 'Carosucan Norte', 'Carosucan Sur', 'Coldit', 'Domanpot', ' Dupac ', 'Macalong', 'Palaris', 'Poblacion East', 'Poblacion West', 'San Juan', 'Toboy'] },
                'Balungao': { barangays: [' Angayan ', ' Bangsal ', ' Banzon ', 'Bongalon', 'Buenavista', 'Bugallon', ' Cabongaoan ', ' Calabaan ', ' Capulaan ', ' Esmeralda ', ' Kita-kita ', ' Magolong ', ' Malabong ', ' Marmaray ', 'Maticaa', 'Narra', 'Niog', 'Orence', 'Padre Galo', 'Poblacion', 'Rajal Centro', 'Rajal Norte', 'Rajal Sur', 'San Andres', 'San Aurelio 1st', 'San Aurelio 2nd', 'San Joaquin', 'San Julian', 'San Leon', 'San Marcelino', 'San Miguel', 'San Raymundo', 'San Vicente', 'Santa Barbara', 'Santo Niño', 'Sumera', 'Villar Pereda'] },
                'Bani': { barangays: ['Aporao', 'Arwas', 'Balerin', 'Bani', 'Binacag', 'Cabaruyan', 'Colayo', 'Dacap Norte', 'Dacap Sur', 'Garrita', 'Luac', 'Macabit', 'Masidem Norte', 'Masidem Sur', 'Nagsaing', 'Olanen', 'Paaralan', ' Poblacion ', 'Quibuar', 'San Jose', 'San Miguel', 'San Simon', 'San Vicente', 'Tiep'] },
                'Basista': { barangays: ['Anambongan', 'Baguinday', 'Baluyot', 'Bautista', 'Bayoyong', 'Cabeldatan', 'Calbayog', 'Cayoocan', 'Dumayas', 'Mapolopolo', 'Nangalisan', 'Nansebacan', 'Olea', 'Palma', 'Patacbo', 'Poblacion', 'San Carlos', 'Sinilian 1st', 'Sinilian 2nd'] },
                'Bautista': { barangays: ['Artacho', 'Bautista', 'Cabuaan', 'Cacandongan', 'Diaz', 'Nandacan', 'Poblacion East', 'Poblacion West', 'Sinabaan', 'Vacante'] },
                'Bayambang': { barangays: ['Alinggan', 'Ambayat I', 'Ambayat II', 'Bacariza', 'Balaybuaya', 'Banaban', 'Banas', 'Bani', 'Batangcaoa', 'Beleng', 'Bical Norte', 'Bical Sur', 'Buenlag 1st', 'Buenlag 2nd', 'Cadre Site', 'Carungay', 'Caturay', 'Duera', 'Dusoc', 'Hermosa', 'Idong', 'Inanlorenzana', 'Inirangan', 'Irineo Villar (Cabaruyan)', 'Manambong Norte', 'Manambong Parte', 'Manambong Sur', 'Mangayao', 'Mangatarem', 'Nalsian Norte', 'Nalsian Sur', 'Pangdel', 'Pantol', 'Paragos', 'Poblacion Zone I', 'Poblacion Zone II', 'Poblacion Zone III', 'Poblacion Zone IV', 'Poblacion Zone V', 'Pugo', 'San Gabriel 1st', 'San Gabriel 2nd', 'San Vicente', 'Sangcagulis', 'Sanlibo', 'Sapang', 'Tamaro', 'Tambac', 'Tococ East', 'Tococ West', 'Warding'] },
                'Binalonan': { barangays: ['Balangobong', 'Bued', 'Bugayong', 'Camangaan', 'Canarvacanan', 'Capas', 'Cili', 'Dumayat', 'Linmansangan', 'Mangcasuy', 'Moreno', 'Pasileng Norte', 'Pasileng Sur', 'Poblacion', 'San Felipe Central', 'San Felipe Sur', 'San Pablo', 'Santa Catalina', 'Santa Maria Norte', 'Santa Maria Sur', 'Santiago', 'Santo Niño', 'Sumague', 'Tabuyoc', 'Vacante'] },
                'Binmaley': { barangays: ['Amancoro', 'Balagan', 'Balingasay', 'Buenlag', 'Calit', 'Caloocan Norte', 'Caloocan Sur', 'Camaley', 'Canaoalan', 'Guelew', 'Linoc', 'Manat', 'Nagpandayan', 'Naguilayan East', 'Naguilayan West', 'Pangascasan', 'Patalan', 'Pototan', 'Sabangan', 'Salapingao', 'San Gonzalo', 'San Isidro Norte', 'San Isidro Sur', 'Tombor'] },
                'Bolinao': { barangays: ['Arnedo', 'Balingasay', 'Binabalian', 'Cabungan', 'Catuday', 'Concordia (Poblacion)', 'Culang', ' Dewey ', 'Estanza', 'Germinal (Poblacion)', 'Gubayan', 'Luna (Poblacion)', 'Patar', 'Pilar', 'Poblacion', 'Samang Norte', 'Samang Sur', 'Taliwara', 'Tara', 'Victory'] },
                'Bugallon': { barangays: ['Angarian', 'Asinan', 'Baguinday', 'Balegong', 'Bangi', 'Boglongan', 'Boned', 'Buenlag', 'Cabayaoasan', 'Cabualan', 'Calantipay', 'Carayungan', 'Gueset', 'Laguit Padilla', 'Magtaking', 'Manlocboc', 'Pantar', 'Poblacion', 'Portic', 'Salomague', 'Samat', 'Talogtog', 'Tonton', 'Umanday'] },
                'Burgos': { barangays: ['Anapao (Bur)', 'Antique', 'Bilis', 'Cabcaburao', 'Ilio-ilio (Iliw-iliw)', 'Inerangan', 'New Poblacion', 'Old Poblacion', 'Palogpoc', 'Pangalangan', 'Papallasen', 'Poblacion', 'Pogoruac', 'San Miguel', 'San Pascual', 'San Vicente', 'Sapa Grande', 'Taguitic'] },
                'Calasiao': { barangays: ['Ambon', 'Bacao', 'Baguio', 'Bolong', 'Bonifacio', 'Bued', 'Calaocan', 'Caranglaan', 'Dinalaoan', 'Kingking', 'Las Attaras', 'Longboan', 'Lubang', 'Malabago', 'Nailan', 'Nancalobasaan', 'Nalsian', 'Pangaoan', 'Parasio', 'Poblacion East', 'Poblacion West', 'Pugo', 'Rizal', 'San Miguel', 'San Vicente', 'Talba', 'Vacante'] },
                'Dasol': { barangays: ['Alacayao', 'Ambon', 'Anga', 'Bagongdaw', 'Bonbonon', 'Cabanas', 'Colosas', 'Dasol', 'Eguia', 'Gais-Guipe', 'Hermosa', 'Macalang', 'Malapas', 'Masi', 'Osmeña', 'Pangapisan', 'Poblacion', 'Ranao', 'San Vicente', 'Sangla', 'Tambobong'] },
                'Infanta': { barangays: ['Babuyan', 'Bagoong', 'Balayang', 'Binday', 'Botigue', 'Catambacan', 'Colambot', 'Dungay', 'Inmalog', 'Maya', 'Naguilayan', 'Nantangalan', 'Poblacion', 'Potot', 'Tagudin', 'Vitol'] },
                'Labrador': { barangays: ['Baculong', 'Bongalon', 'Bolo', 'Ilolong', 'Laois', 'Longa', 'Magsaysay', 'Poblacion', 'Poyao', 'San Jose', 'San Jose Norte', 'San Roque', 'San Vicente', 'Unip'] },
                'Laoac': { barangays: ['Anis', 'Balligi', 'Banuar', 'Botique', 'Cabilaoan', 'Calaocan', 'Domingo Alarcio', 'Ilolong', 'Inlog', 'Labayug', 'Leleng', 'Ligua', 'Longa', 'Manila', 'Poblacion', 'Talogtog', 'Turac'] },
                'Lingayen': { barangays: ['Ali We Kwek', 'Baay', 'Balangobong', 'Balococ', 'Bantayan', 'Basing', 'Ca Pandanan', 'Domalandan Center', 'Domalandan East', 'Domalandan West', 'Dorongan', 'Dulag', 'Estanza', 'La Sip', 'Libsong East', 'Libsong West', 'Malawa', 'Malimpuec', 'Maniboc', 'Matalava', 'Naguelguel', 'Namolan', 'Pangapisan North', 'Pangapisan Sur', 'Poblacion', 'Quibaol', 'Rosario', 'Sabangan', 'Talogtog', 'Tonton', 'Tumbar', 'Wawa'] },
                'Mabini': { barangays: ['Abad Santos', 'Alegria', 'T. Arlan', 'Bailan', 'Garcia', 'Libertad', 'Mabini', 'Mabuhay', 'Magsaysay', 'Rizal', 'Tang Bo'] },
                'Malasiqui': { barangays: ['Agdao', 'Aligui', 'Amacalan', 'Anolid', 'Apaya', 'Asin East', 'Asin West', 'Bacundao East', 'Bacundao West', 'Balite', 'Banawang', 'Bani', 'Bocacliw', 'Bocboc East', 'Bocboc West', 'Bolaoit', 'Buenlag East', 'Buenlag West', 'Cabalitian', 'Cainan', 'Canan Norte', 'Canan Sur', 'Cawayan Bugtong', 'Colayo', 'Dampay', 'Guilig', 'Ingalagala', 'Lacanlacan East', 'Lacanlacan West', 'Lipper', 'Longalong', 'Loqueb Este', 'Loqueb Norte', 'Loqueb Sur', 'Malabac', 'Mancilang', 'Matolong', 'Palapar Norte', 'Palapar Sur', 'Pasima', 'Payar', 'Polong Norte', 'Polong Sur', 'Potiocan', 'San Julian', 'Talospatang', 'Tomling', 'Ungib', 'Villacorta'] },
                'Manaoag': { barangays: ['Acao', 'Aglipay', 'Aloragat', 'Anas', 'Apalep', 'Baguinay', 'Barang', 'Baritao', 'Bisal', 'Bongal', 'Cabalitian', 'Cabaruan', 'Calamagui', 'Caramutan', 'Lelemaan', 'Licsi', 'Lipit Norte', 'Lipit Sur', 'Longalong', 'Matolong', 'Mermer', 'Nalsian', 'Oraan East', 'Oraan West', 'Pantal', 'Pao', 'Parian', 'Poblacion', 'Pugaro', 'San Inocencio', 'San Jose', 'San Ramon', 'San Roque', 'San Vicente', 'Santa Ines', 'Santa Maria', 'Tabora East', 'Tabora West', 'Tebuel', 'Vinalesa']},
                'Mangaldan': { barangays: ['Alitaya','Amansangan East','Amansangan West','Anolid','Banaoang','Bongalon','Buenlag','David','Gueguesangen','Guiguilonen','Inlambo','Lanas','Landas Macayug','Lomboy','Macayug','Malabago','Navaluan','Nibaliw Central','Nibaliw East','Nibaliw West','Osiac','Poblacion','Salay','Talogtog','Tolonguat'] },
                'Mapandan': { barangays: ['Amano Diaz', 'Aserda', 'Balolong', 'Banaoang', 'Bolaoen', 'Cabalitian', 'Golden Sea Mobile Village', 'Guilig', 'Imbo', 'Luyan (Luyan East)', 'Nilombot', 'Poblacion', 'Primicias', 'Santa Maria', 'Torres'] },
                'Mangatarem': { barangays: ['Andangin', 'Arellano St.', 'Auditorio', 'Baculong Norte', 'Baculong Sur', 'Banaoang', 'Bayanihan', 'Benteng Norte', 'Benteng Sur', 'Bongliw', 'Bueno', 'Bunlalacao', 'Burgos St.', 'Cabangaran', 'Cabaruan', 'Cacaritan', 'Calabayan', 'Calzada', 'Caravilla', 'Casantiagoan', 'Castillejos', 'Catarataraan', 'Cawayan Bugtong', 'Cayanga', 'Cortes', 'Diaw', 'Estacion St.', 'General Luna St.', 'Guilig', 'Jackpot', 'Lawak Langka', 'Linmansangan', 'Lomboy', 'Luna St.', 'Manaoac', 'Maravilla', 'Maria St.', 'Navalas', 'Nipa', 'Nisom St.', 'Orence', 'Pacalat', 'Palaris', 'Palayan East', 'Palayan West', 'Pangascasan', 'Poblacion', 'Polintay', 'Quetegan St.', 'Ramos St.', 'Rang-Ay St.', 'Revolucion St.', 'Roxas St.', 'Salavante', 'San Antonio', 'San Juan Arao', 'San Roque', 'Santa Barbara St.', 'Santa Cruz St.', 'Santo Niño', 'Sauz', 'Sinapaoan', 'Tagac', 'Talogtog', 'Tococ Barikir', 'Torres Bugallon'] },
                'Natividad': { barangays: ['Batchelor East', 'Batchelor West', 'Burgos ( formerly Calamagui )', ' Cacabugaoan ', 'Canarem', ' Luna ', ' Poblacion East ', ' Poblacion West ', ' Salud ', ' San Eugenio ', ' San Macario Norte', ' San Macario Sur ', ' San Maximo ', ' Sinaoan East ', ' Sinaoan West ', ' Sudlon ( formerly Digdig )', 'Turac'] },
                'Pozorrubio': { barangays: [' Agat ', ' Amagbagan ', ' Ambalangan ', ' Anao ', ' Bagoong ', ' Balacag ', ' Banding ', ' Bangar ', ' Batakil ', ' Bobonan ', ' Cablong ', ' Casanayan ', ' Kaong ', ' Imboas ', ' Inoman ', ' Ligayat ', ' Maambal ', ' Malasin ', ' Nagsimbaan ', ' Narvacan I ', ' Narvacan II ', ' Palacpalac ', ' Palguyod ', ' Poblacion District I ', ' Poblacion District II ', ' Poblacion District III ', ' Poblacion District IV ', ' Rosario ', ' Sugcong ', ' Tulayan ', ' Villegas '] },
                'Rosales': { barangays: [' Acop ', ' Bakitbakit ', ' Balingcanaway ', ' Cabalaoangan Norte ', ' Cabalaoangan Sur ', ' Camangaan ', ' Capitan Tomas ', ' Carmay ', ' Casanicolasan ', ' Coliling ', ' Don Antonio Village ', ' Guiling ', ' Laoac ', ' Palakipak ', ' Pangaoan ', ' Pindahan ', ' Rabago ', ' Rizal ', ' San Bartolome ', ' San Cristobal ', ' San Luis ', ' San Martin ', ' San Pedro East ', ' San Pedro West ', ' San Roque ', ' San Vicente ', ' San Antonio ', ' San Ignacio ', ' San Isidro ', ' San Jose ', ' San Juan ', ' San Manuel ', ' San Miguel ', ' San Nicolas ', ' San Pio ', ' Santa Barbara ', ' Santa Maria ', ' Santa Monica '] },
                'San Fabian': { barangays: [ 'Angio', ' Asao ', ' Baay ', ' Bacao ', ' Balingueo ', ' Banaoang ', ' Binday ', ' Bolasi ', ' Cabaruan ', ' Cayanga ', ' Colisao ', ' Gomotoc ', ' Inmalog ', ' Inapalan ', ' Inarangan ', ' Inmalobo ', ' Lekep Butao ', ' Lipit Norte ', ' Lipit Sur ', ' Longalong ', ' Mabilao ', ' Nibaliw ', ' Palapad ', ' Poblacion ', ' Rabon ', ' Salay ', ' Tempra ', ' Tiblong '] },
                'San Jacinto': { barangays: [' Bagong ', ' Balasiao ', ' Cababuyan ', ' Calaguiman ', ' Casibong ', ' Imbo ', ' Labit ', ' Lasing ', ' Macayug ', ' Mamarlao ', ' Nalsian ', ' Paldong ', ' Pozo ', ' Santo Tomas ', ' Tagumising '] },
                'San Manuel': { barangays: [' Agno ', ' Balingasay ', ' Baracbac ', ' Cabaritan ', ' Cabatuan ', ' Cabilocaan ', ' Colayo ', ' Danac ', ' Don Matias ', ' Guiset Norte ', ' Guiset Sur ', ' Lapalo ', ' Narra ', ' Pacpaco ', ' Paraiso ', ' Pao ', ' San Bonifacio ', ' San Francisco ', ' San Roque ', ' Santa Cruz '] },
                'San Nicolas': { barangays: [' Baras ', ' Cabalitian ', ' Cacabugaoan ', ' Calanutian ', ' Camindoroan ', ' Casili ', ' Catuguing ', ' Malico ', ' Nining ', ' Poblacion ', ' Salpad ', ' San Roque ', ' Santa Maria ', ' Santo Cristo ', ' Sobol ', ' Tagudin '] },
                'San Quintin': { barangays: [' Alac ', ' Balayao ', ' Banawang ', ' Cabangaran ', ' Cabulalaan ', ' Caronoan ', ' Casantamariaan ', ' Gonzalo ', ' Labuan ', ' Laguit ', ' Maasin ', ' Mantacdang ', ' Nangapugan ', ' Palasigui ', ' Poblacion ', ' Ungib '] },
                'Santa Barbara': { barangays: [' Alibago ', ' Balingueo ', ' Banzal ', ' Botao ', ' Cablong ', ' Carusocan ', ' Dalongue ', ' Erfe ', ' Gueguesangen ', ' Leet ', ' Malanay ', ' Minien East ', ' Minien West ', ' Nilombot ', ' Patayac ', ' Payas ', ' Primicias ', ' Sapang ', ' Sonquil ', ' Tebag East ', ' Tebag West ', ' Tuliao '] },
                'Santa Maria': { barangays: [' Bali ', ' Caboluan ', ' Cal-litang ', ' Canarem ', ' Capitan ', ' Libsong ', ' Namagbagan ', ' Paitan ', ' Pugot ', ' Samon ', ' San Alejandro ', ' San Aurelio ', ' San Isidro ', ' San Vicente ', ' Santa Cruz ', ' Santo Tomas '] },
                'Santo Tomas': { barangays: [' Artacho ', ' Balaoc ', ' Barbasa ', ' Barleon ', ' Basilio ', ' Cabaluyan ', ' Carmencita ', ' La Luna ', ' Poblacion East ', ' Poblacion West ', ' Salvacion ', ' San Agustin ', ' San Antonio ', ' San Basilio ', ' San Eugenio ', ' San Isidro ', ' San Jose ', ' San Marcos ', ' Santo Tomas '] },
                'Sison': { barangays: [' Agat ', ' Alibeng ', ' Amagbagan ', ' Artacho ', ' Asan Sur ', ' Bulaoen East ', ' Bulaoen West ', ' Cabaritan ', ' Calunetan ', ' Esperanza ', ' Inmalog ', ' Lelemaan ', ' Lower Pawing ', ' Macalong ', ' Paldit ', ' Pindangan ', ' Poblacion Central ', ' Sagunto ', ' Upper Pawing '] },
                'Sual': { barangays: [' Baquioen ', ' Baybay Norte ', ' Baybay Sur ', ' Cabalitian ', ' Calumbuyan ', ' Camagsingalan ', ' Caoayan ', ' Capas ', ' Dangla ', ' Longos ', ' Pangascasan ', ' Poblacion ', ' Santo Domingo ', ' Seselangen ', ' Sioasio East ', ' Sioasio West ', ' Victoria '] },
                'Tayug': { barangays: [' Agno ', ' Amistad ', ' Anlong ', ' Aurora ', ' Banaoang ', ' Barangobong ', ' Carriedo ', ' Cayan West ', ' Diaz ', ' Magallanes ', ' Panganiban ', ' Poblacion East ', ' Poblacion West ', ' Saleng ', ' Santo Domingo '] },
                'Umingan': { barangays: ['Agapay', 'Alo-o', 'Amaronan', 'Annam', 'Antipolo', 'Cabalitian', 'Cabaruan', 'Carosalesan', 'Casilan', 'Caurdanetaan', 'Concepcion', 'Del Carmen East', 'Del Carmen West', 'Forbes', 'Lawak East', 'Lawak West', 'Lito', 'Lubong', 'Mantacdang', 'Maseil-seil', 'Nancalabasaan', 'Pangangaan', 'Poblacion East', 'Poblacion West', 'Ricos', 'San Andres', 'San Juan', 'San Leon', 'San Pablo', 'San Vicente', 'Santa Maria', 'Siblong', 'Tanggal Sawang'] },
                'Urbiztondo': { barangays: ['Angatel', 'Balangay', 'Baug', 'Bayaoas', 'Binuangan', 'Bisocol', 'Cabaruan', 'Camambugan', 'Casantiagoan', 'Catablan', 'Cayambanan', 'Galarin', 'Gueteb', 'Malayo', 'Malibong', 'Pasibi East', 'Pasibi West', ' Pisuac', 'Poblacion', 'Real', 'Salavante', 'Sawat'] },
                'Villasis': { barangays: ['Alcala', 'Amamperez', 'Apaya', 'Awai', 'Bacag', 'Barang', 'Barraca', 'Capulaan', 'Caramutan', 'La Paz', 'Labit', 'Lipay', 'Lomboy', 'Piaz', 'Puelay', 'San Blas', 'San Nicolas', 'Tombod'] },
        };

        // ✅ Populate town dropdown
        const townSelect = document.getElementById('town');
        Object.keys(pangasinanData).forEach(town => {
            const option = document.createElement('option');
            option.value = town;
            option.textContent = town;
            townSelect.appendChild(option);
        });

        // ✅ Barangay auto-update
        document.getElementById('town').addEventListener('change', function() {
            const barangaySelect = document.getElementById('barangay');
            barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
            const selectedTown = this.value;
            if (pangasinanData[selectedTown]) {
                pangasinanData[selectedTown].barangays.forEach(b => {
                    const option = document.createElement('option');
                    option.value = b;
                    option.textContent = b;
                    barangaySelect.appendChild(option);
                });
            }
        });
    </script>

</x-guest-layout>
