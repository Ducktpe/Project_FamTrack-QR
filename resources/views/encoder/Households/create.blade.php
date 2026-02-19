<!DOCTYPE html>
<html>
<head>
    <title>Register New Household</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; max-width: 900px; }
        h1 { color: #0038A8; }
        h2 { color: #CE1126; margin-top: 30px; border-bottom: 2px solid #0038A8; padding-bottom: 5px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input[type="text"], input[type="date"], input[type="tel"], select, textarea {
            width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;
        }
        input[type="checkbox"] { margin-right: 5px; }
        .row { display: flex; gap: 15px; }
        .row .form-group { flex: 1; }
        .btn { padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-primary { background-color: #0038A8; color: white; }
        .btn-success { background-color: #28a745; color: white; }
        .btn-secondary { background-color: #6c757d; color: white; }
        .btn-danger { background-color: #dc3545; color: white; }
        .member-card { border: 2px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 8px; background: #f9f9f9; position: relative; }
        .remove-member { position: absolute; top: 10px; right: 10px; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .alert-danger { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <h1>Register New Household</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Please fix these errors:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('encoder.households.store') }}">
        @csrf

        <h2>Household Head Information (RBI)</h2>

        <div class="row">
            <div class="form-group">
                <label>Full Name (Last, First, MI) *</label>
                <input type="text" name="household_head_name" value="{{ old('household_head_name') }}" required placeholder="Dela Cruz, Juan A.">
            </div>
        </div>

        <div class="row">
            <div class="form-group">
                <label>Sex *</label>
                <select name="sex" required>
                    <option value="">-- Select --</option>
                    <option value="Male" {{ old('sex') == 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ old('sex') == 'Female' ? 'selected' : '' }}>Female</option>
                </select>
            </div>
            <div class="form-group">
                <label>Birthday *</label>
                <input type="date" name="birthday" value="{{ old('birthday') }}" required max="{{ date('Y-m-d') }}">
            </div>
            <div class="form-group">
                <label>Civil Status *</label>
                <select name="civil_status" required>
                    <option value="">-- Select --</option>
                    <option value="Single" {{ old('civil_status') == 'Single' ? 'selected' : '' }}>Single</option>
                    <option value="Married" {{ old('civil_status') == 'Married' ? 'selected' : '' }}>Married</option>
                    <option value="Widowed" {{ old('civil_status') == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                    <option value="Separated" {{ old('civil_status') == 'Separated' ? 'selected' : '' }}>Separated</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Contact Number</label>
            <input type="tel" name="contact_number" value="{{ old('contact_number') }}" placeholder="09171234567">
        </div>

        <h2>Address</h2>

        <div class="row">
            <div class="form-group">
                <label>House / Lot Number</label>
                <input type="text" name="house_number" value="{{ old('house_number') }}" placeholder="Blk 1 Lot 5">
            </div>
            <div class="form-group">
                <label>Street / Purok</label>
                <input type="text" name="street_purok" value="{{ old('street_purok') }}" placeholder="Purok 3">
            </div>
        </div>

        <div class="row">
            <div class="form-group">
                <label>Barangay *</label>
                <input type="text" name="barangay" value="{{ old('barangay', 'Barangay Poblacion') }}" required>
            </div>
            <div class="form-group">
                <label>Municipality / City *</label>
                <input type="text" name="municipality" value="{{ old('municipality', 'Naic') }}" required>
            </div>
            <div class="form-group">
                <label>Province *</label>
                <input type="text" name="province" value="{{ old('province', 'Cavite') }}" required>
            </div>
        </div>

        <h2>DSWD / Listahanan Information</h2>

        <div class="form-group">
            <label>Listahanan Household ID (if enrolled)</label>
            <input type="text" name="listahanan_id" value="{{ old('listahanan_id') }}" placeholder="e.g. 1234-5678-9012">
        </div>

        <div class="form-group">
            <label><input type="checkbox" name="is_4ps_beneficiary" value="1" {{ old('is_4ps_beneficiary') ? 'checked' : '' }}> Pantawid Pamilyang Pilipino Program (4Ps) Beneficiary</label>
        </div>

        <div class="form-group">
            <label><input type="checkbox" name="is_pwd" value="1" {{ old('is_pwd') ? 'checked' : '' }}> Has Person with Disability (PWD) member</label>
        </div>

        <div class="form-group">
            <label><input type="checkbox" name="is_senior" value="1" {{ old('is_senior') ? 'checked' : '' }}> Has Senior Citizen (60+) member</label>
        </div>

        <div class="form-group">
            <label><input type="checkbox" name="is_solo_parent" value="1" {{ old('is_solo_parent') ? 'checked' : '' }}> Has Solo Parent member</label>
        </div>

        <h2>Family Members (Optional — can be added later)</h2>
        <p style="color: #666; font-size: 14px;">Add other family members living in this household (excluding the household head)</p>

        <div id="members-container"></div>

        <button type="button" onclick="addMember()" class="btn btn-secondary">+ Add Family Member</button>

        <hr style="margin: 30px 0;">

        <button type="submit" class="btn btn-success">✓ Register Household</button>
        <a href="{{ route('encoder.households.index') }}" class="btn btn-secondary">Cancel</a>
    </form>

    <script>
        let memberIndex = 0;

        function addMember() {
            const container = document.getElementById('members-container');
            const memberCard = document.createElement('div');
            memberCard.className = 'member-card';
            memberCard.id = `member-${memberIndex}`;
            
            memberCard.innerHTML = `
                <button type="button" class="btn btn-danger btn-sm remove-member" onclick="removeMember(${memberIndex})">✕ Remove</button>
                <h3>Family Member ${memberIndex + 1}</h3>
                
                <div class="row">
                    <div class="form-group">
                        <label>Full Name (Last, First, MI) *</label>
                        <input type="text" name="members[${memberIndex}][full_name]" required placeholder="Dela Cruz, Maria A.">
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <label>Relationship to Head *</label>
                        <select name="members[${memberIndex}][relationship]" required>
                            <option value="">-- Select --</option>
                            <option value="Spouse">Spouse</option>
                            <option value="Son">Son</option>
                            <option value="Daughter">Daughter</option>
                            <option value="Parent">Parent</option>
                            <option value="Sibling">Sibling</option>
                            <option value="Grandchild">Grandchild</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Sex *</label>
                        <select name="members[${memberIndex}][sex]" required>
                            <option value="">-- Select --</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Birthday *</label>
                        <input type="date" name="members[${memberIndex}][birthday]" required max="${new Date().toISOString().split('T')[0]}">
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <label>Occupation</label>
                        <input type="text" name="members[${memberIndex}][occupation]" placeholder="Student / Employed / etc">
                    </div>
                    <div class="form-group">
                        <label>Educational Attainment</label>
                        <select name="members[${memberIndex}][educational_attainment]">
                            <option value="">-- Select --</option>
                            <option value="No Schooling">No Schooling</option>
                            <option value="Elementary">Elementary</option>
                            <option value="High School">High School</option>
                            <option value="Vocational">Vocational</option>
                            <option value="College">College</option>
                            <option value="Postgraduate">Postgraduate</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label><input type="checkbox" name="members[${memberIndex}][is_pwd]" value="1"> Person with Disability (PWD)</label>
                </div>

                <div class="form-group">
                    <label><input type="checkbox" name="members[${memberIndex}][is_student]" value="1"> Currently a Student</label>
                </div>
            `;

            container.appendChild(memberCard);
            memberIndex++;
        }

        function removeMember(index) {
            document.getElementById(`member-${index}`).remove();
        }
    </script>
</body>
</html>