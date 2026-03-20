{{--
=======================================================================
  BLADE: households/create.blade.php
  ROUTE: POST /households  →  HouseholdController@store
=======================================================================
  DB TABLES WRITTEN ON SUBMIT:
    1. households               ← all Section 1 fields
    2. household_risk_profiles  ← Section 3 (disaster + economic)
    3. nuclear_families         ← one row per fam[N] block
    4. family_members           ← one row per fam[N][m][M]
    5. family_member_details    ← one row per member (vuln + employment)

  CONTROLLER STUB (HouseholdController@store):
  ─────────────────────────────────────────────
  public function store(Request $request)
  {
      DB::transaction(function () use ($request) {

          // 1. Create household
          $hh = Household::create([
              'serial_code'        => generateSerial(),
              'household_head_name'=> $request->input('fam.1.family_head'),
              'sex'                => $request->input('fam.1.m.1.sex'),
              'birthday'           => $request->input('fam.1.m.1.birthday'),
              'email'              => $request->email,
              'barangay'           => $request->barangay,
              'barangay_area'      => $request->barangay_area,
              'street_purok'       => $request->location,
              'location'           => $request->location,
              'latitude'           => $request->latitude,
              'longitude'          => $request->longitude,
              'coordinates_image'  => $request->coordinates_image,
              'year_built'         => $request->year_built,
              'housing_type'       => $request->housing_type,
              'housing_material'   => $request->housing_material,
              'ownership_type'     => $request->ownership_type,
              'electricity_source' => $request->electricity_source,
              'water_source'       => $request->water_source,
              'toilet_access'      => $request->toilet_access,
              'waste_disposal'     => $request->waste_disposal,
              'is_4ps_beneficiary' => 0,
              'is_pwd'             => 0,
              'is_senior'          => 0,
              'is_solo_parent'     => 0,
              'encoded_by'         => auth()->id(),
              'municipality'       => 'Naic',
              'province'           => 'Cavite',
          ]);

          // 2. Create risk profile
          HouseholdRiskProfile::create([
              'household_id'         => $hh->id,
              'early_warning'        => $request->early_warning ?? 0,
              'ews_sources'          => implode(',', $request->input('ews_sources', [])),
              'hazard_awareness'     => $request->hazard_awareness ?? 0,
              'income_average'       => $request->income_average,
              'literacy_rate'        => $request->literacy_rate,
              'financial_assistance' => $request->financial_assistance ?? 0,
              'access_info'          => $request->access_info ?? 0,
              'relocate_willingness' => $request->relocate_willingness ?? 0,
              'remarks'              => $request->remarks,
          ]);

          // 3. Loop nuclear families
          foreach ($request->input('fam', []) as $fi => $famData) {
              $nf = NuclearFamily::create([
                  'household_id' => $hh->id,
                  'family_name'  => $famData['family_name'] ?? null,
                  'family_type'  => $famData['family_type'] ?? null,
                  'family_head'  => $famData['family_head'] ?? null,
              ]);

              // 4 & 5. Loop members
              foreach ($famData['m'] ?? [] as $mi => $m) {
                  $member = FamilyMember::create([
                      'household_id'           => $hh->id,
                      'nuclear_family_id'      => $nf->id,
                      'full_name'              => $m['full_name'] ?? null,
                      'relationship'           => $m['relationship'] ?? null,
                      'civil_status'           => $m['civil_status'] ?? null,
                      'sex'                    => $m['sex'] ?? null,
                      'birthday'               => $m['birthday'] ?? null,
                      'is_pwd'                 => 0,
                      'is_student'             => 0,
                      'educational_attainment' => $m['educational_attainment'] ?? null,
                  ]);

                  FamilyMemberDetail::create([
                      'family_member_id'  => $member->id,
                      'vulnerable_sector' => $m['vuln_sector'] ?? null,
                      'vuln_registered'   => $m['vuln_registered'] ?? null,
                      'vuln_id_number'    => $m['vuln_id_number'] ?? null,
                      'is_lgbtqia'        => isset($m['is_lgbtqia']) ? 1 : 0,
                      'employment_status' => $m['employment_status'] ?? null,
                      'job_title'         => $m['job_title'] ?? null,
                  ]);
              }
          }
      });

      return redirect()->route('households.index')
          ->with('success', 'Household record saved successfully.');
  }
=======================================================================
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <title>MDRRMO Naic — Household Profile Form</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&family=PT+Serif:wght@700&display=swap" rel="stylesheet">
    <style>
        :root {
            --blue:#1B3F7A;--blue-dark:#122D5A;--blue-light:#2459A8;--blue-pale:#EAF0FA;
            --yellow:#F5C518;--yellow-dark:#D4A800;--white:#FFFFFF;
            --gray-50:#F7F8FA;--gray-100:#F0F2F5;--gray-200:#DEE2E8;
            --gray-400:#9AA3B0;--gray-600:#5A6372;--gray-800:#2C3340;
            --red:#C0392B;--green:#16A34A;--sidebar-w:260px;
        }
        *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
        html,body{height:100%;font-family:'Open Sans',sans-serif;background:var(--gray-100);color:var(--gray-800);font-size:14px;}
        .shell{display:grid;grid-template-rows:36px 76px 1fr 48px;grid-template-columns:var(--sidebar-w) 1fr;grid-template-areas:"topbar topbar" "header header" "sidebar main" "footer footer";height:100vh;overflow:hidden;}
        /* TOP BAR */
        .topbar{grid-area:topbar;background:var(--blue-dark);display:flex;align-items:center;justify-content:space-between;padding:0 24px;z-index:100;}
        .topbar-left{font-size:11px;color:rgba(255,255,255,.5);letter-spacing:.3px;}
        .topbar-right{display:flex;align-items:center;gap:20px;}
        .clock-inline{font-size:12px;font-weight:600;color:var(--yellow);letter-spacing:1px;font-variant-numeric:tabular-nums;}
        .clock-date-inline{font-size:11px;color:rgba(255,255,255,.45);}
        .status-indicator{display:flex;align-items:center;gap:6px;font-size:11px;color:rgba(255,255,255,.45);}
        .status-indicator::before{content:'';width:6px;height:6px;border-radius:50%;background:#4CAF50;box-shadow:0 0 5px #4CAF50;animation:blink 2s infinite;}
        @keyframes blink{0%,100%{opacity:1}50%{opacity:.4}}
        /* HEADER */
        header{grid-area:header;background:var(--white);border-bottom:3px solid var(--yellow);box-shadow:0 2px 6px rgba(0,0,0,.08);display:flex;align-items:center;padding:0 28px;gap:14px;z-index:90;}
        .hamburger{display:none;background:none;border:none;cursor:pointer;padding:6px;margin-left:-4px;border-radius:4px;color:var(--blue-dark);flex-shrink:0;transition:background .15s;}
        .hamburger:hover{background:var(--blue-pale);}
        .hamburger svg{width:22px;height:22px;display:block;}
        .header-logos{display:flex;align-items:center;gap:12px;flex-shrink:0;}
        .header-logos img{height:54px;width:54px;object-fit:contain;}
        .logo-divider{width:1px;height:44px;background:var(--gray-200);}
        .header-text{margin-left:4px;}
        .header-org{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--gray-400);margin-bottom:2px;}
        .header-title{font-family:'PT Serif',serif;font-size:18px;font-weight:700;color:var(--blue-dark);line-height:1.2;}
        .header-sub{font-size:11px;color:var(--gray-600);margin-top:2px;}
        .header-spacer{flex:1;}
        .header-right{display:flex;align-items:center;gap:12px;flex-shrink:0;}
        /* LANG TOGGLE */
        .lang-toggle{display:flex;align-items:center;background:var(--gray-100);border:1px solid var(--gray-200);border-radius:20px;padding:3px;gap:2px;}
        .lang-btn{font-family:'Open Sans',sans-serif;font-size:11px;font-weight:700;letter-spacing:.5px;padding:5px 14px;border-radius:16px;border:none;cursor:pointer;transition:all .2s ease;color:var(--gray-400);background:transparent;}
        .lang-btn.active{background:var(--blue);color:var(--white);box-shadow:0 2px 6px rgba(27,63,122,.25);}
        .lang-btn:not(.active):hover{color:var(--blue);background:var(--blue-pale);}
        .header-user-badge{display:flex;align-items:center;gap:10px;padding:8px 14px;background:#FFF7ED;border:1px solid #D97706;border-radius:4px;}
        .user-avatar{width:32px;height:32px;border-radius:50%;background:#D97706;display:flex;align-items:center;justify-content:center;color:#FFF;font-weight:700;font-size:13px;flex-shrink:0;}
        .user-name{font-size:13px;font-weight:600;color:var(--blue-dark);line-height:1.2;}
        .user-role{font-size:10px;color:#D97706;text-transform:uppercase;letter-spacing:.5px;}
        /* SIDEBAR */
        .sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:200;pointer-events:none;}
        .sidebar-overlay.active{display:block!important;pointer-events:auto;}
        .sidebar{grid-area:sidebar;background:var(--white);border-right:1px solid var(--gray-200);display:flex;flex-direction:column;overflow-y:auto;position:relative;}
        .sidebar-close{display:none;position:absolute;top:12px;right:12px;background:var(--gray-100);border:1px solid var(--gray-200);border-radius:4px;width:32px;height:32px;align-items:center;justify-content:center;cursor:pointer;z-index:10;color:var(--gray-600);transition:background .15s;}
        .sidebar-close:hover{background:#FEF2F2;color:var(--red);}
        .sidebar-close svg{width:16px;height:16px;}
        .nav-section-label{padding:18px 20px 8px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:var(--gray-400);}
        .nav-item{display:flex;align-items:center;gap:12px;padding:11px 20px;font-size:13.5px;font-weight:500;color:var(--gray-600);text-decoration:none;border-left:3px solid transparent;transition:background .12s,color .12s,border-color .12s;cursor:pointer;}
        .nav-item:hover{background:var(--gray-50);color:var(--blue);border-left-color:var(--blue-light);}
        .nav-item.active{background:var(--blue-pale);color:var(--blue);border-left-color:var(--blue);font-weight:600;}
        .nav-icon{width:17px;height:17px;flex-shrink:0;color:inherit;opacity:.7;}
        .nav-item.active .nav-icon,.nav-item:hover .nav-icon{opacity:1;}
        .nav-badge{margin-left:auto;background:var(--blue);color:#FFF;font-size:9px;font-weight:700;padding:2px 8px;border-radius:10px;letter-spacing:.5px;}
        .sidebar-sep{border:none;border-top:1px solid var(--gray-100);margin:8px 0;}
        .role-notice{margin:12px 14px;background:#FFFAE6;border:1px solid #F5C518;border-left:3px solid #D4A800;padding:10px 12px;border-radius:2px;}
        .role-notice-title{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#92400E;margin-bottom:3px;}
        .role-notice-text{font-size:11px;color:#78350F;line-height:1.5;}
        .sidebar-bottom{margin-top:auto;padding:16px 20px;border-top:1px solid var(--gray-200);}
        .logout-btn{width:100%;font-family:'Open Sans',sans-serif;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:1px;background:var(--blue);color:#FFF;border:none;padding:10px 16px;border-radius:4px;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:background .15s;}
        .logout-btn:hover{background:var(--red);}
        /* MAIN */
        .main-content{grid-area:main;background:var(--gray-50);overflow-y:auto;padding:28px 32px;}
        .page-titlebar{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid var(--gray-200);gap:12px;}
        .page-breadcrumb{font-size:11px;color:var(--gray-400);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;}
        .page-breadcrumb span{color:var(--blue-light);}
        .page-h1{font-family:'PT Serif',serif;font-size:22px;font-weight:700;color:var(--blue-dark);}
        .page-sub{font-size:12px;color:var(--gray-600);margin-top:3px;}
        .page-date{font-size:12px;color:var(--gray-600);text-align:right;flex-shrink:0;}
        .page-date strong{display:block;font-size:13px;font-weight:600;color:var(--gray-800);white-space:nowrap;}
        /* FORM CARDS */
        .form-card{background:var(--white);border:1px solid var(--gray-200);margin-bottom:20px;}
        .form-card-header{padding:14px 20px;border-bottom:1px solid var(--gray-100);background:var(--gray-50);display:flex;align-items:center;gap:10px;}
        .form-card-dot{width:8px;height:8px;border-radius:50%;background:var(--yellow);border:2px solid var(--yellow-dark);flex-shrink:0;}
        .form-card-title{font-size:13px;font-weight:600;color:var(--blue-dark);flex:1;}
        .form-card-badge{background:var(--blue-pale);color:var(--blue);font-size:10px;font-weight:700;padding:3px 10px;border-radius:10px;text-transform:uppercase;letter-spacing:.5px;}
        .form-card-body{padding:24px 28px;}
        .form-row{display:grid;gap:16px;margin-bottom:20px;}
        .form-row.cols-2{grid-template-columns:1fr 1fr;}
        .form-row.cols-3{grid-template-columns:1fr 1fr 1fr;}
        .form-row.cols-1{grid-template-columns:1fr;}
        .form-row.cols-4{grid-template-columns:1fr 1fr 1fr 1fr;}
        .form-group{display:flex;flex-direction:column;gap:6px;}
        .form-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--gray-600);}
        .req{color:var(--red);margin-left:2px;}
        .form-input,.form-select,.form-textarea{font-family:'Open Sans',sans-serif;font-size:13px;color:var(--gray-800);background:var(--white);border:1px solid var(--gray-200);border-radius:3px;padding:8px 12px;transition:border-color .15s,box-shadow .15s;width:100%;}
        .form-input:focus,.form-select:focus,.form-textarea:focus{outline:none;border-color:var(--blue-light);box-shadow:0 0 0 3px rgba(36,89,168,.1);}
        .form-select{appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%239AA3B0' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 10px center;padding-right:30px;}
        .form-textarea{resize:vertical;min-height:70px;}
        .check-group{display:flex;flex-wrap:wrap;gap:8px 20px;padding:10px 14px;background:var(--gray-50);border:1px solid var(--gray-200);border-radius:3px;}
        .check-item{display:flex;align-items:flex-start;gap:6px;cursor:pointer;}
        .check-item input{accent-color:var(--blue);width:14px;height:14px;cursor:pointer;flex-shrink:0;margin-top:2px;}
        .check-item .cl{font-size:12px;color:var(--gray-700);cursor:pointer;user-select:none;line-height:1.4;}
        .form-section-divider{border:none;border-top:2px dashed var(--gray-200);margin:28px 0 24px;}
        .form-section-title{font-family:'PT Serif',serif;font-size:14px;font-weight:700;color:var(--blue-dark);margin-bottom:16px;padding-bottom:8px;border-bottom:2px solid var(--blue-pale);display:flex;align-items:center;gap:8px;}
        .fsn{display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;background:var(--blue);color:#FFF;font-family:'Open Sans',sans-serif;font-size:11px;font-weight:700;border-radius:50%;flex-shrink:0;}
        /* Member table */
        .member-table{width:100%;border-collapse:collapse;font-size:12px;margin-top:8px;}
        .member-table th{background:var(--blue);color:#FFF;padding:8px 10px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;white-space:nowrap;}
        .member-table td{padding:6px 8px;border-bottom:1px solid var(--gray-100);vertical-align:top;}
        .member-table tr:hover td{background:var(--blue-pale);}
        .member-table .form-input,.member-table .form-select{padding:5px 8px;font-size:11px;}
        .btn-add-member{display:inline-flex;align-items:center;gap:6px;margin-top:12px;background:var(--blue-pale);color:var(--blue);border:1px dashed var(--blue-light);border-radius:3px;padding:7px 16px;font-size:12px;font-weight:600;cursor:pointer;font-family:'Open Sans',sans-serif;transition:background .15s;}
        .btn-add-member:hover{background:#d4e4f7;}
        .btn-remove{background:none;border:none;color:var(--gray-400);cursor:pointer;padding:4px;border-radius:3px;transition:color .15s;}
        .btn-remove:hover{color:var(--red);}
        .id-input{font-size:11px;padding:4px 8px;border:1px solid var(--gray-200);border-radius:3px;font-family:'Open Sans',sans-serif;width:120px;}
        .id-input:focus{outline:none;border-color:var(--blue-light);}
        /* ── Individual Section ── */
        .ind-section-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px;}
        .ind-resident-pill{display:flex;align-items:center;gap:8px;background:var(--blue-pale);border:1px solid #c3d8f5;border-radius:24px;padding:6px 16px 6px 10px;}
        .ind-resident-pill svg{color:var(--blue);flex-shrink:0;}
        .ind-resident-pill label{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--blue);white-space:nowrap;}
        .ind-resident-pill input{width:56px;font-size:13px;font-weight:700;color:var(--blue-dark);border:none;background:transparent;outline:none;font-family:'Open Sans',sans-serif;text-align:center;}
        @keyframes nf-appear{from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:translateY(0);}}
        .nf-card{border:1px solid var(--gray-200);border-radius:8px;margin-bottom:16px;overflow:hidden;box-shadow:0 2px 8px rgba(27,63,122,.06);animation:nf-appear .25s ease;}
        .nf-card-header{display:flex;align-items:center;gap:8px;padding:10px 14px 10px 18px;background:linear-gradient(135deg,#f0f5ff 0%,#e8f0fa 100%);border-bottom:1px solid var(--gray-200);transition:background .15s;}
        .nf-card-header:hover{background:linear-gradient(135deg,#e4edf9 0%,#dce8f7 100%);}
        .nf-toggle-area{display:flex;align-items:center;gap:12px;flex:1;min-width:0;cursor:pointer;user-select:none;}
        .nf-stripe{width:4px;height:36px;border-radius:3px;flex-shrink:0;}
        .nf-stripe-1{background:#1B3F7A;} .nf-stripe-2{background:#2e86de;} .nf-stripe-3{background:#10ac84;}
        .nf-stripe-4{background:#ee5a24;} .nf-stripe-5{background:#8854d0;} .nf-stripe-6{background:#e0a800;}
        .nf-num{display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;background:var(--blue);color:#FFF;font-size:12px;font-weight:700;border-radius:50%;flex-shrink:0;box-shadow:0 2px 6px rgba(27,63,122,.3);}
        .nf-header-text{flex:1;min-width:0;}
        .nf-label{font-size:13px;font-weight:700;color:var(--blue-dark);line-height:1.2;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
        .nf-sublabel{font-size:10px;color:var(--gray-400);margin-top:1px;}
        .nf-pills{display:flex;align-items:center;gap:6px;flex-wrap:wrap;}
        .nf-type-badge{font-size:10px;font-weight:700;background:var(--blue);color:#FFF;padding:3px 10px;border-radius:10px;letter-spacing:.3px;white-space:nowrap;}
        .nf-count-badge{font-size:10px;font-weight:600;background:#FFF;color:var(--gray-600);border:1px solid var(--gray-200);padding:3px 10px;border-radius:10px;white-space:nowrap;}
        .nf-header-actions{display:flex;align-items:center;gap:8px;flex-shrink:0;}
        .nf-remove-btn{background:#fff0f0;border:1px solid #f5c6c6;border-radius:6px;color:#c0392b;cursor:pointer;padding:5px 10px;font-size:11px;font-weight:600;display:flex;align-items:center;gap:4px;transition:all .15s;font-family:'Open Sans',sans-serif;}
        .nf-remove-btn:hover{background:#fde8e8;border-color:#c0392b;}
        .nf-toggle{width:18px;height:18px;color:var(--blue-light);transition:transform .25s cubic-bezier(.4,0,.2,1);flex-shrink:0;}
        .nf-toggle.collapsed{transform:rotate(-90deg);}
        .nf-body{background:#fff;overflow:hidden;max-height:3000px;opacity:1;transition:max-height .35s ease,opacity .25s ease;}
        .nf-body.nf-collapsed{max-height:0;opacity:0;pointer-events:none;}
        .nf-body-inner{padding:20px 22px;}
        .nf-meta{display:grid;grid-template-columns:1.2fr 1fr 1fr;gap:16px;margin-bottom:20px;padding:16px;background:var(--gray-50);border:1px solid var(--gray-100);border-radius:6px;}
        @media(max-width:700px){.nf-meta{grid-template-columns:1fr;}}
        .nf-members-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;padding-bottom:10px;border-bottom:2px solid var(--blue-pale);}
        .nf-members-title{font-family:'PT Serif',serif;font-size:13px;font-weight:700;color:var(--blue-dark);display:flex;align-items:center;gap:7px;}
        .nf-members-title .fsn{width:20px;height:20px;font-size:10px;}
        .member-table{width:100%;border-collapse:separate;border-spacing:0;font-size:12px;margin-top:2px;}
        .member-table thead th{background:var(--blue-dark);color:#fff;padding:9px 10px;text-align:left;font-size:9.5px;font-weight:700;text-transform:uppercase;letter-spacing:.9px;white-space:nowrap;}
        .member-table thead th:first-child{border-radius:6px 0 0 0;width:36px;text-align:center;}
        .member-table thead th:last-child{border-radius:0 6px 0 0;width:36px;}
        .member-table tbody tr:nth-child(even) td{background:#fafbfd;}
        .member-table tbody tr:hover td{background:#eef4ff !important;}
        .member-table td{padding:7px 8px;border-bottom:1px solid var(--gray-100);vertical-align:middle;}
        .member-table td:first-child{text-align:center;font-weight:700;color:var(--blue);font-size:11px;background:#f4f8ff;}
        .member-table .form-input,.member-table .form-select{padding:5px 8px;font-size:11px;border-radius:4px;}
        .btn-add-member{display:inline-flex;align-items:center;gap:7px;margin-top:12px;background:#fff;color:var(--blue);border:1.5px dashed var(--blue-light);border-radius:6px;padding:8px 18px;font-size:12px;font-weight:600;cursor:pointer;font-family:'Open Sans',sans-serif;transition:all .15s;}
        .btn-add-member:hover{background:var(--blue-pale);border-style:solid;}
        .btn-remove{background:none;border:none;color:var(--gray-400);cursor:pointer;padding:5px;border-radius:4px;transition:all .15s;display:flex;align-items:center;justify-content:center;}
        .btn-remove:hover{color:var(--red);background:#fff0f0;}
        .id-input{font-size:11px;padding:4px 8px;border:1px solid var(--gray-200);border-radius:4px;font-family:'Open Sans',sans-serif;width:120px;}
        .id-input:focus{outline:none;border-color:var(--blue-light);}
        .btn-add-family{display:flex;align-items:center;justify-content:center;gap:10px;width:100%;padding:14px;background:transparent;border:2px dashed #c3d8f5;border-radius:8px;color:var(--blue-light);font-family:'Open Sans',sans-serif;font-size:13px;font-weight:600;cursor:pointer;transition:all .2s;margin-top:4px;}
        .btn-add-family:hover{background:var(--blue-pale);border-color:var(--blue);color:var(--blue);}
        @media(max-width:640px){.nf-pills{display:none;}.nf-label{font-size:12px;}}

        .form-actions{display:flex;align-items:center;justify-content:flex-end;gap:12px;padding:20px 28px;background:var(--gray-50);border-top:1px solid var(--gray-200);}
        .btn-primary{font-family:'Open Sans',sans-serif;font-size:13px;font-weight:600;background:var(--blue);color:#FFF;border:none;padding:10px 28px;border-radius:3px;cursor:pointer;display:flex;align-items:center;gap:8px;transition:background .15s;}
        .btn-primary:hover{background:var(--blue-dark);}
        .btn-secondary{font-family:'Open Sans',sans-serif;font-size:13px;font-weight:600;background:var(--white);color:var(--gray-600);border:1px solid var(--gray-200);padding:10px 20px;border-radius:3px;cursor:pointer;transition:background .15s,color .15s;}
        .btn-secondary:hover{background:var(--gray-100);color:var(--gray-800);}
        /* FOOTER */
        footer{grid-area:footer;background:var(--blue-dark);border-top:3px solid var(--yellow);display:flex;align-items:center;justify-content:space-between;padding:0 24px;gap:8px;z-index:100;}
        .footer-left{font-size:11px;color:rgba(255,255,255,.4);}
        .footer-left strong{color:rgba(255,255,255,.7);}
        .footer-center{font-size:10px;color:rgba(255,255,255,.2);letter-spacing:1px;text-transform:uppercase;}
        .fb-link{display:flex;align-items:center;gap:6px;font-size:11px;color:rgba(255,255,255,.4);text-decoration:none;transition:color .15s;white-space:nowrap;}
        .fb-link:hover{color:var(--yellow);}
        .fb-link svg{width:13px;height:13px;}
        ::-webkit-scrollbar{width:5px;}::-webkit-scrollbar-track{background:var(--gray-100);}::-webkit-scrollbar-thumb{background:var(--gray-200);border-radius:4px;}
        /* RESPONSIVE */
        /* ── 900px: tablet — sidebar becomes drawer ── */
        @media (max-width: 900px) {
            .shell {
                grid-template-rows: 36px auto 1fr 48px;
                grid-template-columns: 1fr;
                grid-template-areas: "topbar" "header" "main" "footer";
                height: 100vh;
                overflow: hidden;
            }
            .sidebar {
                grid-area: unset;
                position: fixed;
                top: 0; left: 0; bottom: 0;
                width: var(--sidebar-w);
                z-index: 300;
                transform: translateX(-100%);
                transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 4px 0 20px rgba(0,0,0,0.15);
            }
            .sidebar.open { transform: translateX(0); }
            .sidebar-overlay { display: block; }
            .sidebar-close { display: flex; }
            .sidebar .nav-section-label { padding-top: 52px; }

            .hamburger { display: flex; }

            header { padding: 0 16px; gap: 10px; }
            .header-logos img { height: 44px; width: 44px; }
            .header-title { font-size: 15px; }
            .header-sub { display: none; }
            .header-user-badge { padding: 6px 10px; gap: 8px; }
            .user-name { font-size: 12px; }
            .user-role { display: none; }

            .topbar { padding: 0 16px; }
            .topbar-left { display: none; }

            .main-content { padding: 20px 16px; }

            /* Form grids */
            .form-row.cols-3 { grid-template-columns: 1fr 1fr; }
            .form-row.cols-4 { grid-template-columns: 1fr 1fr; }

            /* Member table: allow horizontal scroll */
            .member-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }

            /* Nuclear family head panel */
            #head_panel_1 > div[style*="grid-template-columns:repeat(4"] {
                grid-template-columns: 1fr 1fr !important;
            }
        }

        /* ── 640px: large phone ── */
        @media (max-width: 640px) {
            .topbar { justify-content: flex-end; }
            .clock-date-inline { display: none; }
            .status-indicator { display: none; }

            header { padding: 0 12px; gap: 8px; }
            .header-logos img { height: 36px; width: 36px; }
            .logo-divider { display: none; }
            .header-logos img:last-child { display: none; }
            .header-org { display: none; }
            .header-title { font-size: 13px; line-height: 1.3; }
            .header-user-badge { padding: 5px 8px; }
            .user-avatar { width: 28px; height: 28px; font-size: 11px; }
            .user-name { font-size: 11px; }

            .main-content { padding: 16px 12px; }
            .form-card-body { padding: 16px; }

            .page-titlebar { flex-direction: column; align-items: flex-start; gap: 6px; }
            .page-h1 { font-size: 18px; }
            .page-date { text-align: left; }

            /* All multi-col form rows collapse to 1 col */
            .form-row.cols-2,
            .form-row.cols-3,
            .form-row.cols-4 { grid-template-columns: 1fr; }

            /* Nuclear family meta row */
            .nf-meta { grid-template-columns: 1fr !important; }

            /* Head panel full width */
            #head_panel_1 > div[style*="grid-template-columns"] {
                grid-template-columns: 1fr 1fr !important;
            }

            /* Nuclear family pills hidden */
            .nf-pills { display: none; }
            .nf-label { font-size: 12px; }

            footer { padding: 0 12px; }
            .footer-center { display: none; }
            .footer-left { font-size: 10px; }
        }

        /* ── 480px: small phone ── */
        @media (max-width: 480px) {
            .form-actions { flex-direction: column-reverse; gap: 8px; }
            .btn-primary, .btn-secondary { width: 100%; justify-content: center; }

            .nf-card-header { flex-wrap: wrap; gap: 8px; }
            .nf-header-actions { width: 100%; justify-content: flex-end; }

            /* Head panel single column on small phones */
            #head_panel_1 > div[style*="grid-template-columns"] {
                grid-template-columns: 1fr !important;
            }
            #head_panel_1 .form-group[style*="grid-column:span 2"] {
                grid-column: span 1 !important;
            }

            .lang-toggle { display: none; }
        }
    </style>
</head>
<body>
<div class="shell">
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<!-- TOP BAR -->
<div class="topbar">
    <div class="topbar-left">Republic of the Philippines &nbsp;|&nbsp; Province of Cavite &nbsp;|&nbsp; Municipality of Naic</div>
    <div class="topbar-right">
        <span class="clock-date-inline" id="top-date">—</span>
        <span class="clock-inline" id="top-time">00:00:00</span>
        <span class="status-indicator">System Online</span>
    </div>
</div>

<!-- HEADER -->
<header>
    <button class="hamburger" onclick="openSidebar()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>
    <div class="header-logos">
        <img src="/images/mdrrmo-logo.png" alt="MDRRMO" onerror="this.style.display='none'">
        <div class="logo-divider"></div>
        <img src="/images/naic-seal.png" alt="Naic" onerror="this.style.display='none'">
    </div>
    <div class="header-text">
        <div class="header-org">Office of the Municipal DRRMO</div>
        <div class="header-title">MDRRMO — Naic, Cavite</div>
        <div class="header-sub">Municipal Disaster Risk Reduction and Management Office</div>
    </div>
    <div class="header-spacer"></div>
    <div class="header-right">
        <div class="lang-toggle" role="group">
            <button class="lang-btn active" id="btn-en" onclick="setLang('en')">EN</button>
            <button class="lang-btn" id="btn-tl" onclick="setLang('tl')">TL</button>
            <button class="lang-btn" id="btn-mix" onclick="setLang('mix')">MIX</button>
        </div>
        <div class="header-user-badge">
            <div class="user-avatar">E</div>
            <div>
                <div class="user-name">Encoder</div>
                <div class="user-role">Data Entry Access</div>
            </div>
        </div>
    </div>
</header>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <button class="sidebar-close" onclick="closeSidebar()" aria-label="Close navigation">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
    </button>

    <div class="nav-section-label">Encoder Menu</div>

    <a href="{{ route('encoder.dashboard') }}" class="nav-item" onclick="closeSidebar()">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
            <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
        </svg>
        Dashboard
    </a>
    <a href="{{ route('encoder.households.create') }}" class="nav-item active" onclick="closeSidebar()">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
            <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
        </svg>
        Add Household
        <span class="nav-badge">Form</span>
    </a>
    <a href="{{ route('encoder.households.index') }}" class="nav-item" onclick="closeSidebar()">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path d="M9 22V12h6v10"/>
        </svg>
        List of Households
    </a>

    <hr class="sidebar-sep">

    <div class="role-notice">
        <div class="role-notice-title">&#9432; Encoder Access</div>
        <div class="role-notice-text">You can create and update family profiles. QR code generation and distribution logs are managed by the Admin.</div>
    </div>

    <div class="sidebar-bottom">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/>
                </svg>
                Logout
            </button>
        </form>
    </div>
</aside>

<!-- MAIN -->
<main class="main-content">
    <div class="page-titlebar">
        <div>
            <div class="page-breadcrumb">Home / <span id="bc-households">Households</span> / <span id="bc-add" style="color:var(--blue-light)">Add New Household</span></div>
            <div class="page-h1" id="pg-title">Household Profile Form</div>
            <div class="page-sub" id="pg-sub">RBI — Residential &amp; Beneficiary Information Form</div>
        </div>
        <div class="page-date"><span id="today-lbl">Today</span><strong id="main-date">—</strong></div>
    </div>

    {{-- POST to your Laravel route: route('households.store') --}}
    <form id="householdForm" method="POST" action="{{ route('encoder.households.store') }}" enctype="multipart/form-data" onsubmit="handleSubmit(event)">
    @csrf
    {{-- Always Naic, Cavite --}}
    <input type="hidden" name="municipality" value="Naic">
    <input type="hidden" name="province" value="Cavite">

    <!-- SECTION 1 -->
    <div class="form-card">
        <div class="form-card-header">
            <div class="form-card-dot"></div>
            <div class="form-card-title" id="s1-title">Section 1 — Population / Household Information</div>
            <div class="form-card-badge" id="badge-required">Required</div>
        </div>
        <div class="form-card-body">
            <div class="form-section-title"><span class="fsn">A</span> <span id="sec-a">Location &amp; Contact</span></div>

            {{-- ── Household Head (required by households table) ── --}}
            <div class="form-row cols-3">
                <div class="form-group" style="grid-column:span 2;">
                    <label class="form-label">Household Head Full Name <span class="req">*</span></label>
                    {{-- DB: households.household_head_name --}}
                    <input type="text" class="form-input" name="household_head_name" required
                        id="inp-hhname" value="{{ old('household_head_name') }}" placeholder="Last Name, First Name M.I." oninput="syncSection1ToNF1()">
                </div>
                <div class="form-group">
                    <label class="form-label">Contact Number</label>
                    {{-- DB: households.contact_number --}}
                    <input type="text" class="form-input" name="contact_number"
                        value="{{ old('contact_number') }}" placeholder="e.g. 09XX-XXX-XXXX">
                </div>
            </div>

            <div class="form-row cols-2">
                <div class="form-group">
                    <label class="form-label">National ID</label>
                    {{-- DB: households.national_id --}}
                    <input type="text" class="form-input" name="national_id"
                        value="{{ old('national_id') }}" placeholder="National ID (if any)">
                </div>
                <div class="form-group">
                    <label class="form-label" id="lbl-email">Email Address</label>
                    {{-- DB: households.email --}}
                    <input type="email" class="form-input" name="email" id="inp-email" value="{{ old('email') }}">
                </div>
            </div>
            <hr class="form-section-divider" style="margin-top:4px;">

            <div class="form-row cols-2">
                <div class="form-group">
                    <label class="form-label"><span id="lbl-barangay">Barangay</span> <span class="req">*</span></label>
                    {{-- DB: households.barangay --}}
                    <select class="form-select" name="barangay" required id="sel-barangay">
                        <option value="" id="opt-brgy">— Select Barangay —</option>
                        <option>Bagong Kalsada</option><option>Balsahan</option><option>Bancaan</option>
                        <option>Bucana Malaki</option><option>Bucana Sasahan</option><option>Calubcob</option>
                        <option>Capt. C. Nazareno (Poblacion)</option><option>Gombalza (Poblacion)</option>
                        <option>Halang</option><option>Humbac</option><option>Ibayo Estacion</option>
                        <option>Ibayo Silangan</option><option>Kanluran</option><option>Latoria</option>
                        <option>Labac</option><option>Mabolo</option><option>Malainen Bago</option>
                        <option>Malainen Luma</option><option>Makina</option><option>Molino</option>
                        <option>Munting Mapino</option><option>Muzon</option><option>Palangue 2 &amp; 3</option>
                        <option>Palangue Central</option><option>Sabang</option><option>San Roque</option>
                        <option>Santulan</option><option>Sapa</option><option>Timalan Balsahan</option>
                        <option>Timalan Concepcion</option>
                    </select>
                </div>
            </div>
            <div class="form-row cols-3">
                <div class="form-group">
                    <label class="form-label"><span id="lbl-area">Barangay Area in Hectares (Zoning / Purok)</span> <span class="req">*</span></label>
                    {{-- DB: households.barangay_area --}}
                    <input type="text" class="form-input" name="barangay_area" id="inp-area" value="{{ old('barangay_area') }}">
                </div>
                <div class="form-group">
                    <label class="form-label"><span id="lbl-location">Location / Subdivision / Sitio / Street Name</span> <span class="req">*</span></label>
                    {{-- DB: households.location (maps to street_purok area) --}}
                    <input type="text" class="form-input" name="location" id="inp-location" value="{{ old('location') }}">
                </div>
                <div class="form-group">
                    <label class="form-label" id="lbl-year">When Was the House Built?</label>
                    {{-- DB: households.year_built --}}
                    <input type="number" class="form-input" name="year_built" id="inp-year" min="1900" max="2025" value="{{ old('year_built') }}">
                </div>
            </div>
            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
            <style>
                .coord-map-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--gray-600);margin-bottom:6px;display:flex;align-items:center;gap:8px;}
                #location-map{width:100%;height:260px;border-radius:6px;border:1px solid var(--gray-200);cursor:crosshair;z-index:1;}
                .map-toolbar{display:flex;align-items:center;gap:8px;margin-bottom:8px;flex-wrap:wrap;}
                .btn-gps{display:inline-flex;align-items:center;gap:6px;background:var(--blue);color:#fff;border:none;border-radius:4px;padding:7px 14px;font-size:12px;font-weight:600;cursor:pointer;font-family:'Open Sans',sans-serif;transition:background .15s;}
                .btn-gps:hover{background:var(--blue-dark);} .btn-gps:disabled{background:var(--gray-400);cursor:not-allowed;}
                .btn-gps svg{width:14px;height:14px;flex-shrink:0;}
                .gps-status{font-size:11px;color:var(--gray-600);font-style:italic;}
                .coord-inputs{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
                .coord-manual-hint{font-size:11px;color:var(--gray-400);margin-top:4px;}
                .img-upload-box{border:2px dashed var(--gray-200);border-radius:6px;padding:16px;text-align:center;background:var(--gray-50);cursor:pointer;transition:border-color .15s,background .15s;position:relative;}
                .img-upload-box:hover{border-color:var(--blue-light);background:var(--blue-pale);}
                .img-upload-box input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%;}
                .img-upload-icon{width:32px;height:32px;margin:0 auto 8px;color:var(--gray-400);}
                .img-upload-text{font-size:12px;color:var(--gray-600);font-weight:600;}
                .img-upload-sub{font-size:11px;color:var(--gray-400);margin-top:2px;}
                .img-preview-wrap{margin-top:10px;display:none;position:relative;}
                .img-preview-wrap img{width:100%;max-height:160px;object-fit:cover;border-radius:4px;border:1px solid var(--gray-200);}
                .img-preview-remove{position:absolute;top:6px;right:6px;background:rgba(0,0,0,.55);color:#fff;border:none;border-radius:50%;width:22px;height:22px;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:14px;line-height:1;}
            </style>
            {{-- Location Photo --}}
            <div class="form-row cols-1" style="margin-bottom:12px;">
                <div class="form-group">
                    <label class="form-label" id="lbl-coord-img">Location Photo <span style="font-weight:400;color:var(--gray-400);text-transform:none;letter-spacing:0;">(optional — map screenshot or on-site photo)</span></label>
                    <div class="img-upload-box" onclick="document.getElementById('coord-img-file').click()">
                        <input type="file" id="coord-img-file" name="coordinates_image" accept="image/*" onchange="previewCoordImg(event)">
                        <svg class="img-upload-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        <div class="img-upload-text" id="img-upload-text">Click to upload location photo</div>
                        <div class="img-upload-sub">JPG, PNG, WEBP — max 5 MB</div>
                    </div>
                    <div class="img-preview-wrap" id="imgPreviewWrap">
                        <img id="imgPreview" src="" alt="Location preview">
                        <button type="button" class="img-preview-remove" onclick="removeCoordImg()">✕</button>
                    </div>
                    {{-- coordinates_image is submitted directly via the file input above --}}
                </div>
            </div>
            {{-- Map Pin --}}
            <div class="form-group" style="margin-bottom:20px;">
                <div class="coord-map-label">📍 Pin Location on Map <span style="font-weight:400;color:var(--gray-400);text-transform:none;letter-spacing:0;font-size:11px;">— drag the pin or click the map</span></div>
                <div class="map-toolbar">
                    <button type="button" class="btn-gps" id="btn-gps" onclick="useMyLocation()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3"/><circle cx="12" cy="12" r="8" stroke-dasharray="2 2"/></svg>
                        Use My Current Location
                    </button>
                    <span class="gps-status" id="gps-status"></span>
                </div>
                <div id="location-map"></div>
                <div class="coord-inputs" style="margin-top:12px;">
                    <div class="form-group">
                        <label class="form-label"><span id="lbl-lat">Latitude</span></label>
                        <input type="text" class="form-input" name="latitude" id="inp-lat" placeholder="e.g. 14.3124" value="{{ old('latitude') }}" oninput="onManualCoord()">
                        <div class="coord-manual-hint">Auto-filled by map pin · or type manually</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><span id="lbl-lon">Longitude</span></label>
                        <input type="text" class="form-input" name="longitude" id="inp-lng" placeholder="e.g. 120.7606" value="{{ old('longitude') }}" oninput="onManualCoord()">
                        <div class="coord-manual-hint">Auto-filled by map pin · or type manually</div>
                    </div>
                </div>
            </div>
            <script>
            (function(){
                const DEFAULT_LAT=14.3128,DEFAULT_LNG=120.7611;
                const map=L.map('location-map',{center:[DEFAULT_LAT,DEFAULT_LNG],zoom:15});
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{attribution:'© OpenStreetMap',maxZoom:19}).addTo(map);
                const pinIcon=L.divIcon({html:`<div style="width:28px;height:38px;filter:drop-shadow(0 2px 4px rgba(0,0,0,.35))"><svg viewBox="0 0 28 38" xmlns="http://www.w3.org/2000/svg"><path d="M14 0C6.27 0 0 6.27 0 14c0 9.33 14 24 14 24S28 23.33 28 14C28 6.27 21.73 0 14 0z" fill="#1B3F7A"/><circle cx="14" cy="14" r="6" fill="#fff"/></svg></div>`,iconSize:[28,38],iconAnchor:[14,38],className:''});
                let marker=null;
                function setPin(lat,lng){
                    lat=parseFloat(lat.toFixed(6));lng=parseFloat(lng.toFixed(6));
                    document.getElementById('inp-lat').value=lat;
                    document.getElementById('inp-lng').value=lng;
                    if(marker){marker.setLatLng([lat,lng]);}
                    else{marker=L.marker([lat,lng],{icon:pinIcon,draggable:true}).addTo(map);marker.on('dragend',e=>{const p=e.target.getLatLng();setPin(p.lat,p.lng);});}
                }
                map.on('click',e=>setPin(e.latlng.lat,e.latlng.lng));
                const oLat=document.getElementById('inp-lat').value,oLng=document.getElementById('inp-lng').value;
                if(oLat&&oLng){setPin(parseFloat(oLat),parseFloat(oLng));map.setView([parseFloat(oLat),parseFloat(oLng)],17);}
                window.useMyLocation=function(){
                    const btn=document.getElementById('btn-gps'),st=document.getElementById('gps-status');
                    if(!navigator.geolocation){st.textContent='Geolocation not supported.';return;}
                    btn.disabled=true;st.textContent='Getting location…';
                    navigator.geolocation.getCurrentPosition(p=>{setPin(p.coords.latitude,p.coords.longitude);map.setView([p.coords.latitude,p.coords.longitude],18);st.textContent='✓ Found (±'+Math.round(p.coords.accuracy)+'m)';btn.disabled=false;},err=>{st.textContent='⚠ '+(err.code===1?'Permission denied.':'Could not get location.');btn.disabled=false;},{enableHighAccuracy:true,timeout:12000});
                };
                window.onManualCoord=function(){
                    const lat=parseFloat(document.getElementById('inp-lat').value),lng=parseFloat(document.getElementById('inp-lng').value);
                    if(!isNaN(lat)&&!isNaN(lng)&&lat>=-90&&lat<=90&&lng>=-180&&lng<=180){setPin(lat,lng);map.setView([lat,lng],17);}
                };
            })();
            function previewCoordImg(e){const file=e.target.files[0];if(!file)return;if(file.size>5*1024*1024){alert('File too large. Max 5MB.');e.target.value='';return;}const r=new FileReader();r.onload=ev=>{document.getElementById('imgPreview').src=ev.target.result;document.getElementById('imgPreviewWrap').style.display='block';document.getElementById('img-upload-text').textContent=file.name;};r.readAsDataURL(file);}
            function removeCoordImg(){document.getElementById('coord-img-file').value='';document.getElementById('imgPreview').src='';document.getElementById('imgPreviewWrap').style.display='none';document.getElementById('img-upload-text').textContent='Click to upload location photo';}
            </script>
            <hr class="form-section-divider">
            <div class="form-section-title"><span class="fsn">B</span> <span id="sec-b">Housing Unit</span></div>
            <div class="form-row cols-2">
                <div class="form-group">
                    <label class="form-label"><span id="lbl-housing">Type of Housing Unit</span> <span class="req">*</span></label>
                    {{-- DB: households.housing_type --}}
                    <div class="check-group" id="grp-housing"></div>
                </div>
                <div class="form-group">
                    <label class="form-label"><span id="lbl-material">Material to Make It</span> <span class="req">*</span></label>
                    {{-- DB: households.housing_material --}}
                    <div class="check-group" id="grp-material"></div>
                </div>
            </div>
            <div class="form-row cols-2">
                <div class="form-group">
                    <label class="form-label"><span id="lbl-ownership">Type of Ownership</span> <span class="req">*</span></label>
                    {{-- DB: households.ownership_type --}}
                    <div class="check-group" id="grp-ownership"></div>
                </div>
                <div class="form-group">
                    <label class="form-label" id="lbl-electricity">Source of Electricity</label>
                    {{-- DB: households.electricity_source --}}
                    <div class="check-group" id="grp-electricity"></div>
                </div>
            </div>
            <hr class="form-section-divider">
            <div class="form-section-title"><span class="fsn">C</span> <span id="sec-c">Utilities &amp; Sanitation</span></div>
            <div class="form-row cols-3">
                <div class="form-group">
                    <label class="form-label"><span id="lbl-water">Source of Water</span> <span class="req">*</span></label>
                    {{-- DB: households.water_source --}}
                    <div class="check-group" id="grp-water"></div>
                </div>
                <div class="form-group">
                    <label class="form-label"><span id="lbl-toilet">Access to Toilet Facilities</span> <span class="req">*</span></label>
                    {{-- DB: households.toilet_access --}}
                    <div class="check-group" id="grp-toilet"></div>
                </div>
                <div class="form-group">
                    <label class="form-label" id="lbl-waste">Waste Disposal System</label>
                    {{-- DB: households.waste_disposal --}}
                    <div class="check-group" id="grp-waste"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- INDIVIDUAL SECTION -->
    <div class="form-card">
        <div class="form-card-header">
            <div class="form-card-dot"></div>
            <div class="form-card-title" id="s2-title">Individual Section — Household Members</div>
            <div class="form-card-badge" id="badge-member">Per Member</div>
        </div>
        <div class="form-card-body">
            <!-- Section header row -->
            <div class="ind-section-header">
                <div style="font-size:12px;color:var(--gray-600);" id="residents-hint">Add one block per nuclear family residing in this household.</div>
                <div class="ind-resident-pill">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                    <label id="lbl-num-residents">Total Members</label>
                    <input type="number" name="num_residents" id="numResidents" min="1" max="30" readonly tabindex="-1">
                </div>
            </div>

            <!-- Nuclear Family Cards -->
            <div id="familyList"></div>

            <!-- Add Family CTA -->
            <button type="button" class="btn-add-family" onclick="addNuclearFamily()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                <span id="btn-add-family-lbl">Add Nuclear Family</span>
            </button>
        </div>
    </div>

    <!-- CONTINUATION -->
    <div class="form-card">
        <div class="form-card-header">
            <div class="form-card-dot"></div>
            <div class="form-card-title" id="s3-title">Continuation — Household Risk &amp; Economic Profile</div>
            <div class="form-card-badge" id="badge-all">All Fields</div>
        </div>
        <div class="form-card-body">
            <div class="form-section-title"><span class="fsn">A</span> <span id="sec-d">Disaster Awareness &amp; Access</span></div>
            <div class="form-row cols-2">
                <div class="form-group">
                    <label class="form-label" id="lbl-ews">Access to Early Warning System (EWS)</label>
                    {{-- DB: household_risk_profiles.early_warning (1=Yes,0=No) --}}
                    <div class="check-group">
                        <label class="check-item"><input type="radio" name="early_warning" value="1"> <span class="cl" id="lbl-yes">Yes</span></label>
                        <label class="check-item"><input type="radio" name="early_warning" value="0"> <span class="cl" id="lbl-no">No</span></label>
                    </div>
                    <div style="margin-top:8px;">
                        <label class="form-label" style="margin-bottom:4px;" id="lbl-ews-spec">If Yes, Specify Source:</label>
                        {{-- DB: household_risk_profiles.ews_sources (comma-separated) --}}
                        <div class="check-group">
                            <label class="check-item"><input type="checkbox" name="ews_sources[]" value="tv"> <span class="cl">TV</span></label>
                            <label class="check-item"><input type="checkbox" name="ews_sources[]" value="radio"> <span class="cl" id="lbl-radio">Radio</span></label>
                            <label class="check-item"><input type="checkbox" name="ews_sources[]" value="brgy"> <span class="cl" id="lbl-brgy-ann">Brgy. Announcement</span></label>
                            <label class="check-item"><input type="checkbox" name="ews_sources[]" value="other"> <span class="cl" id="lbl-other-info">Other Info</span></label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label" id="lbl-hazard">Awareness to Hazard Impact &amp; Climate Change</label>
                    {{-- DB: household_risk_profiles.hazard_awareness (1=Yes,0=No) --}}
                    <div class="check-group">
                        <label class="check-item"><input type="radio" name="hazard_awareness" value="1"> <span class="cl" id="lbl-yes2">Yes</span></label>
                        <label class="check-item"><input type="radio" name="hazard_awareness" value="0"> <span class="cl" id="lbl-no2">No</span></label>
                    </div>
                </div>
            </div>
            <hr class="form-section-divider">
            <div class="form-section-title"><span class="fsn">B</span> <span id="sec-e">Economic &amp; Social Profile</span></div>
            <div class="form-row cols-2">
                <div class="form-group">
                    <label class="form-label" id="lbl-income">Income Status (Average)</label>
                    {{-- DB: household_risk_profiles.income_average --}}
                    <input type="number" class="form-input" name="income_average" id="inp-income" value="{{ old('income_average') }}">
                </div>
                <div class="form-group">
                    <label class="form-label" id="lbl-literacy">Literacy Status / Rate (%)</label>
                    {{-- DB: household_risk_profiles.literacy_rate --}}
                    <input type="number" class="form-input" name="literacy_rate" placeholder="0–100" min="0" max="100" value="{{ old('literacy_rate') }}">
                </div>
            </div>
            <div class="form-row cols-2">
                <div class="form-group">
                    <label class="form-label" id="lbl-fin">Access to Financial Assistance</label>
                    {{-- DB: household_risk_profiles.financial_assistance (1=Yes,0=No) --}}
                    <div class="check-group">
                        <label class="check-item"><input type="radio" name="financial_assistance" value="1"> <span class="cl" id="lbl-yes3">Yes</span></label>
                        <label class="check-item"><input type="radio" name="financial_assistance" value="0"> <span class="cl" id="lbl-no3">No</span></label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label" id="lbl-info">Access to Information</label>
                    {{-- DB: household_risk_profiles.access_info (1=Yes,0=No) --}}
                    <div class="check-group">
                        <label class="check-item"><input type="radio" name="access_info" value="1"> <span class="cl" id="lbl-yes4">Yes</span></label>
                        <label class="check-item"><input type="radio" name="access_info" value="0"> <span class="cl" id="lbl-no4">No</span></label>
                    </div>
                </div>
            </div>
            <div class="form-row cols-2">
                <div class="form-group">
                    <label class="form-label" id="lbl-relocate">Capacity and Willingness to Retrofit or Relocate</label>
                    {{-- DB: household_risk_profiles.relocate_willingness (1=Yes,0=No) --}}
                    <div class="check-group">
                        <label class="check-item"><input type="radio" name="relocate_willingness" value="1"> <span class="cl" id="lbl-yes5">Yes</span></label>
                        <label class="check-item"><input type="radio" name="relocate_willingness" value="0"> <span class="cl" id="lbl-no5">No</span></label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label" id="lbl-remarks">Other / Remarks</label>
                    {{-- DB: household_risk_profiles.remarks --}}
                    <textarea class="form-textarea" name="remarks" id="inp-remarks">{{ old('remarks') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- ACTIONS -->
    <div class="form-card">
        <div class="form-actions">
            <button type="button" class="btn-secondary" id="btn-reset" onclick="confirmReset()">Reset Form</button>
            <button type="submit" class="btn-primary" id="btn-save">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                <span id="btn-save-lbl">Save Household Record</span>
            </button>
        </div>
    </div>

    </form>
</main>

<footer>
    <div class="footer-left">&copy; <span id="footer-year"></span> <strong>MDRRMO Naic, Cavite</strong> &mdash; Municipal Disaster Risk Reduction and Management Office</div>
    <div class="footer-center">Republic of the Philippines</div>
    <a class="fb-link" href="https://www.facebook.com/naicmdrrmo" target="_blank" rel="noopener">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
        facebook.com/naicmdrrmo
    </a>
</footer>
</div>

<script>
/* ═══════════════════════════════════════
   TRANSLATION DATA
   ═══════════════════════════════════════ */
const EN = {
    // page
    'bc-add':'Add New Household','pg-title':'Household Profile Form',
    'pg-sub':'RBI — Residential & Beneficiary Information Form','today-lbl':'Today',
    // cards
    's1-title':'Section 1 — Population / Household Information',
    's2-title':'Individual Section — Household Members',
    's3-title':'Continuation — Household Risk & Economic Profile',
    'badge-required':'Required','badge-member':'Per Member','badge-all':'All Fields',
    // sections
    'sec-a':'Location & Contact','sec-b':'Housing Unit','sec-c':'Utilities & Sanitation',
    'sec-d':'Disaster Awareness & Access','sec-e':'Economic & Social Profile',
    // labels
    'lbl-email':'Email Address','lbl-barangay':'Barangay',
    'lbl-area':'Barangay Area in Hectares (Zoning / Purok)',
    'lbl-location':'Location / Subdivision / Sitio / Street Name',
    'lbl-year':'When Was the House Built?',
    'lbl-coord-img':'Coordinates (Image)','lbl-lat':'Coordinates (Latitude)','lbl-lon':'Coordinates (Longitude)',
    'lbl-housing':'Type of Housing Unit','lbl-material':'Material to Make It',
    'lbl-ownership':'Type of Ownership','lbl-electricity':'Source of Electricity',
    'lbl-water':'Source of Water','lbl-toilet':'Access to Toilet Facilities',
    'lbl-waste':'Waste Disposal System',
    'lbl-num-residents':'Number of Individuals (Families) Residing (H/NF)',
    'residents-hint':'1–30 members','members-title':'Members List',
    'lbl-ews':'Access to Early Warning System (EWS)',
    'lbl-ews-spec':'If Yes, Specify Source:',
    'lbl-hazard':'Awareness to Hazard Impact & Climate Change',
    'lbl-income':'Income Status (Average)','lbl-literacy':'Literacy Status / Rate (%)',
    'lbl-fin':'Access to Financial Assistance','lbl-info':'Access to Information',
    'lbl-relocate':'Capacity and Willingness to Retrofit or Relocate',
    'lbl-remarks':'Other / Remarks',
    // table headers
    'th-name':'Name','th-age':'Age','th-bdate':'Birthdate','th-sex':'Sex',
    'th-civil':'Civil Status','th-vuln':'Vulnerable Sector',
    'th-employ':'Employment Status','th-educ':'Educational Attainment',
    // common
    'lbl-yes':'Yes','lbl-no':'No','lbl-yes2':'Yes','lbl-no2':'No',
    'lbl-yes3':'Yes','lbl-no3':'No','lbl-yes4':'Yes','lbl-no4':'No',
    'lbl-yes5':'Yes','lbl-no5':'No',
    'lbl-radio':'Radio','lbl-brgy-ann':'Brgy. Announcement','lbl-other-info':'Other Info',
    // buttons & nav
    'btn-add-lbl':'Add Member','btn-reset':'Reset Form','btn-save-lbl':'Save Household Record',
    'btn-add-family-lbl':'Add Nuclear Family',
    'lbl-family-name':'Family Name / Surname',
    'lbl-family-type':'Family Type',
    'lbl-family-head':'Name of Family Head',
    'sel-famtype':['Nuclear Family','Extended Family','Single Parent Family','Blended Family','Childless Couple','Grandparent-headed','Skipped Generation','Other'],
    'family-lbl':'Nuclear Family',
    'members-title':'Members List',
    'nav-add-household':'Add Household','nav-households':'List of Households','nav-logout':'Logout',
    'role-notice-text':'You can create and update family profiles. QR code generation and distribution logs are managed by the Admin.',
    'bc-households':'Households',
    // opt
    'opt-brgy':'— Select Barangay —',
    // placeholders
    'inp-email':'e.g. household@email.com','inp-area':'e.g. 12.5 ha',
    'inp-location':'Subdivision, Sitio, or Street','inp-year':'Year (e.g. 2005)',
    'inp-coord-img':'Image link or pin','inp-income':'e.g. 15000',
    'inp-remarks':'Additional notes or suggestions...',
    // dynamic option arrays (radio groups)
    'opts-housing':['Apartment','Bungalow','Makeshift','Mobile Home','Townhouse','Mansion','Farmhouse','Duplex','Condo','Villa','Modular Building','Stilt House','Hut','Single Detached'],
    'opts-material':['Concrete','Semi-Concrete','Wood and Light Materials','Recycled Materials'],
    'opts-ownership':['Owned','Rented','Shared','Shared with Renter','Informal Settler Families','Rights'],
    'opts-electricity':['Electric Company','Generator','Solar','Battery','Other'],
    'opts-water':['Shallow Well (50 ft deep – Level 1) e.g. Poso, balon','Deep Well (100 ft deep – Level 1) e.g. Poso, balon','Water Project (Other Source – Level 2)','Maynilad (Level 3)'],
    'opts-toilet':['Yes — Safely Managed (with Septic Tank)','Yes — Basic (not shared with another household)','Yes — Limited (shared by 2 or more households)','Yes — Unimproved (pit / hukay)','No — Open Defecation'],
    'opts-waste':['Open Dump Site','Sanitary Landfill','MRF','Garbage Collection','Other'],
    // member table selects
    'sel-sex':['Male','Female'],
    'sel-civil':['Single','Married','Legally Separated','Widowed'],
    'sel-vuln':['None','Senior','PWD','Solo Parent','4Ps Member','Young (17yo below)','Old (60yo above)'],
    'sel-employ':['Unemployed','Employed – specify job','Part-time','Full-time','Self-employed','Pension / Retired','Freelance','Other'],
    'sel-educ':['Elementary Undergraduate','Elementary Graduate','High School Undergraduate','High School Graduate','Vocational','College Undergraduate','College Graduate','Master','Doctorate','TESDA','Other'],
    'opt-select':'— Select —','opt-male':'Male','opt-female':'Female',
    'vuln-reg':'Registered','vuln-unreg':'Unregistered','vuln-id-ph':'ID Number','vuln-hh-id':'Household ID No.',
    'employ-spec-ph':'Specify job title...',
    'employ-other-ph':'Please specify...',
    'confirm-reset':'Clear all form data?','save-ok':'✓ Record Saved!',
};

const TL = {
    'bc-add':'Magdagdag ng Bagong Sambahayan','pg-title':'Form ng Profayl ng Sambahayan',
    'pg-sub':'RBI — Form ng Impormasyon ng Tirahan at Benepisyaryo','today-lbl':'Ngayon',
    's1-title':'Seksyon 1 — Populasyon / Impormasyon ng Sambahayan',
    's2-title':'Seksyon para sa Indibidwal — Mga Miyembro ng Sambahayan',
    's3-title':'Karagdagang Impormasyon — Panganib at Ekonomiya ng Sambahayan',
    'badge-required':'Kinakailangan','badge-member':'Bawat Miyembro','badge-all':'Lahat ng Field',
    'sec-a':'Lokasyon at Pakikipag-ugnayan','sec-b':'Uri ng Tirahan',
    'sec-c':'Kagamitan at Kalinisan','sec-d':'Kaalaman sa Sakuna at Access',
    'sec-e':'Pang-ekonomiya at Panlipunang Profayl',
    'lbl-email':'Email Address','lbl-barangay':'Barangay',
    'lbl-area':'Lugar ng Barangay sa Ektarya (Zoning / Purok)',
    'lbl-location':'Lokasyon / Subdivision / Sitio / Pangalan ng Kalye',
    'lbl-year':'Kailan Itinayo ang Bahay?',
    'lbl-coord-img':'Koordinasyon (Larawan)','lbl-lat':'Koordinasyon (Latitude)','lbl-lon':'Koordinasyon (Longitude)',
    'lbl-housing':'Uri ng Tirahan','lbl-material':'Materyales na Ginamit sa Pagtatayo',
    'lbl-ownership':'Uri ng Pagmamay-ari','lbl-electricity':'Pinagkukunan ng Kuryente',
    'lbl-water':'Pinagkukunan ng Tubig','lbl-toilet':'Access sa Pasilidad ng Banyo/Kubeta',
    'lbl-waste':'Sistema ng Pagtatapon ng Basura',
    'lbl-num-residents':'Bilang ng Indibidwal (Mga Pamilya) na Naninirahan (H/NF)',
    'residents-hint':'1–30 miyembro','members-title':'Listahan ng Miyembro',
    'lbl-ews':'Access sa Maagang Sistema ng Babala (EWS)',
    'lbl-ews-spec':'Kung Oo, Tukuyin ang Pinagkukunan:',
    'lbl-hazard':'Kaalaman sa Epekto ng Panganib at Pagbabago ng Klima',
    'lbl-income':'Katayuan sa Kita (Average)','lbl-literacy':'Katayuan/Rate ng Literacy (%)',
    'lbl-fin':'Access sa Tulong Pinansyal','lbl-info':'Access sa Impormasyon',
    'lbl-relocate':'Kakayahan at Kagustuhang Mag-retrofit o Maglipat',
    'lbl-remarks':'Iba pa / Mga Puna',
    'th-name':'Pangalan','th-age':'Edad','th-bdate':'Petsa ng Kapanganakan','th-sex':'Kasarian',
    'th-civil':'Katayuang Sibil','th-vuln':'Bulnerableng Sektor',
    'th-employ':'Katayuan sa Trabaho','th-educ':'Pinakamataas na Antas ng Pag-aaral',
    'lbl-yes':'Oo','lbl-no':'Hindi','lbl-yes2':'Oo','lbl-no2':'Hindi',
    'lbl-yes3':'Oo','lbl-no3':'Hindi','lbl-yes4':'Oo','lbl-no4':'Hindi','lbl-yes5':'Oo','lbl-no5':'Hindi',
    'lbl-radio':'Radyo','lbl-brgy-ann':'Anunsyo ng Brgy.','lbl-other-info':'Iba pang Impormasyon',
    'btn-add-lbl':'Magdagdag ng Miyembro','btn-reset':'I-reset ang Form',
    'btn-save-lbl':'I-save ang Rekord ng Sambahayan',
    'btn-add-family-lbl':'Magdagdag ng Nuclear na Pamilya',
    'lbl-family-name':'Pangalan / Apelyido ng Pamilya',
    'lbl-family-type':'Uri ng Pamilya',
    'lbl-family-head':'Pangalan ng Ulo ng Pamilya',
    'sel-famtype':['Nuclear na Pamilya','Pinalawig na Pamilya','Mag-iisang Magulang','Halo-halong Pamilya','Mag-asawang Walang Anak','Pinamumunuan ng Lolo/Lola','Preskong Henerasyon','Iba pa'],
    'family-lbl':'Nuclear na Pamilya',
    'members-title':'Listahan ng Miyembro',
    'nav-add-household':'Magdagdag ng Sambahayan','nav-households':'Listahan ng mga Sambahayan','nav-logout':'Mag-logout',
    'role-notice-text':'Maaari kang lumikha at mag-update ng mga profayl ng pamilya. Ang paglikha ng QR code at mga log ng pamamahagi ay pinamamahalaan ng Admin.',
    'bc-households':'Mga Sambahayan',
    'opt-brgy':'— Pumili ng Barangay —',
    'inp-email':'hal. sambahayan@email.com','inp-area':'hal. 12.5 ektarya',
    'inp-location':'Subdivision, Sitio, o Kalye','inp-year':'Taon (hal. 2005)',
    'inp-coord-img':'Link ng larawan o pin','inp-income':'hal. 15000',
    'inp-remarks':'Karagdagang tala o mungkahi...',
    'opts-housing':['Apartment','Bungalow','Makeshift (Pansamantalang Gawa)','Mobile Home','Townhouse','Mansyon','Bahay-bukid','Duplex','Condo','Villa','Modular na Gusali','Bahay sa Tukod','Kubo','Single Detached'],
    'opts-material':['Kongkreto','Semi-kongkreto','Kahoy at Magaang na Materyales','Mga Recycled na Materyales'],
    'opts-ownership':['Sariling-ari','Inuupahan','Ibinabahagi','Ibinabahagi kasama ang Nangungupahan','Pamilyang Informal Settler','Karapatan'],
    'opts-electricity':['Kumpanya ng Kuryente','Generator','Solar','Baterya','Iba pa'],
    'opts-water':['Mababaw na Balon (50 talampakan ang lalim – Antas 1) hal. Poso, balon','Malalim na Balon (100 talampakan ang lalim – Antas 1) hal. Poso, balon','Proyektong Tubig (Ibang Pinagkukunan – Antas 2)','Maynilad (Antas 3)'],
    'opts-toilet':['Oo — Ligtas na Pinamamahalaan (may Septic Tank)','Oo — Pangunahin (hindi ibinabahagi sa ibang sambahayan)','Oo — Limitado (ibinabahagi ng 2 o higit pang sambahayan)','Oo — Hindi Pinahusay (pit / hukay)','Hindi — Bukas na Pagtatapon ng Dumi'],
    'opts-waste':['Bukas na Pagtatapon ng Basura','Sanitary Landfill','MRF','Koleksyon ng Basura','Iba pa'],
    'sel-sex':['Lalaki','Babae'],
    'sel-civil':['Walang Asawa','May Asawa','Legal na Hiwalay','Balo'],
    'sel-vuln':['Wala','Senior','PWD','Nag-iisang Magulang','Miyembro ng 4Ps','Bata (17 taong gulang pababa)','Matanda (60 taong gulang pataas)'],
    'sel-employ':['Walang Trabaho','May Trabaho – tukuyin ang trabaho','Part-time','Full-time','Negosyante/Sariling Trabaho','Pensiyon/Retirado','Freelance','Iba pa'],
    'sel-educ':['Elementarya Hindi Tapos','Elementarya Tapos','Sekundarya Hindi Tapos','Sekundarya Tapos','Bokasyonal','Kolehiyo Hindi Tapos','Kolehiyo Tapos','Master','Doktorado','TESDA','Iba pa'],
    'opt-select':'— Pumili —','opt-male':'Lalaki','opt-female':'Babae',
    'vuln-reg':'Rehistrado','vuln-unreg':'Hindi Rehistrado','vuln-id-ph':'ID Number','vuln-hh-id':'Household ID No.',
    'employ-spec-ph':'Tukuyin ang trabaho...',
    'employ-other-ph':'Pakitukoy...',
    'confirm-reset':'Burahin ang lahat ng datos sa form?','save-ok':'✓ Nai-save ang Rekord!',
};

// Build MIX: English label (Tagalog)
const MIX = {};
for (const k in EN) {
    const e = EN[k], t = TL[k];
    if (Array.isArray(e) && Array.isArray(t)) {
        MIX[k] = e.map((v,i) => t[i] && t[i]!==v ? `${v} (${t[i]})` : v);
    } else if (typeof e==='string' && typeof t==='string' && t!==e) {
        MIX[k] = `${e} (${t})`;
    } else {
        MIX[k] = e;
    }
}

let LANG = 'en';
function T(k){ return ({en:EN,tl:TL,mix:MIX}[LANG]||EN)[k] ?? EN[k] ?? k; }

/* ─── Option group config ─── */
// name values here map exactly to households table column names
const GRP_CFG = [
    {id:'grp-housing',     key:'opts-housing',     name:'housing_type'},
    {id:'grp-material',    key:'opts-material',    name:'housing_material'},
    {id:'grp-ownership',   key:'opts-ownership',   name:'ownership_type'},
    {id:'grp-electricity', key:'opts-electricity', name:'electricity_source'},
    {id:'grp-water',       key:'opts-water',       name:'water_source'},
    {id:'grp-toilet',      key:'opts-toilet',      name:'toilet_access'},
    {id:'grp-waste',       key:'opts-waste',       name:'waste_disposal'},
];
const GRP_VALS = {
    'opts-housing':['apartment','bungalow','makeshift','mobile_home','townhouse','mansion','farmhouse','duplex','condo','villa','modular','stilt','hut','single_detached'],
    'opts-material':['concrete','semi_concrete','wood_light','recycled'],
    'opts-ownership':['owned','rented','shared','shared_renter','isf','rights'],
    'opts-electricity':['electric_company','generator','solar','battery','other'],
    'opts-water':['shallow_well','deep_well','water_project','maynilad'],
    'opts-toilet':['safely_managed','basic','limited','unimproved','open_defecation'],
    'opts-waste':['open_dump','sanitary_landfill','mrf','garbage','other'],
};

function renderGroups(){
    GRP_CFG.forEach(cfg=>{
        const el=document.getElementById(cfg.id); if(!el) return;
        const opts=T(cfg.key), vals=GRP_VALS[cfg.key];
        const checked=el.querySelector('input:checked')?.value||null;
        el.innerHTML=opts.map((lbl,i)=>`<label class="check-item">
            <input type="radio" name="${cfg.name}" value="${vals[i]}"${checked===vals[i]?' checked':''}>
            <span class="cl">${lbl}</span></label>`).join('');
    });
}

/* ─── Static text map ─── */
const STATIC_IDS = ['bc-add','pg-title','pg-sub','today-lbl','s1-title','s2-title','s3-title',
    'badge-required','badge-member','badge-all','sec-a','sec-b','sec-c','sec-d','sec-e',
    'lbl-email','lbl-barangay','lbl-area','lbl-location','lbl-year','lbl-coord-img','lbl-lat','lbl-lon',
    'lbl-housing','lbl-material','lbl-ownership','lbl-electricity','lbl-water','lbl-toilet','lbl-waste',
    'lbl-num-residents','residents-hint','members-title','lbl-ews','lbl-ews-spec','lbl-hazard',
    'lbl-income','lbl-literacy','lbl-fin','lbl-info','lbl-relocate','lbl-remarks',
    'th-name','th-age','th-bdate','th-sex','th-civil','th-vuln','th-employ','th-educ',
    'lbl-yes','lbl-no','lbl-yes2','lbl-no2','lbl-yes3','lbl-no3','lbl-yes4','lbl-no4','lbl-yes5','lbl-no5',
    'lbl-radio','lbl-brgy-ann','lbl-other-info',
    'btn-add-lbl','btn-reset','btn-save-lbl',
    'nav-add-household','nav-households','nav-logout','role-notice-text','bc-households'];

const PLACEHOLDER_IDS = {
    'inp-email':'inp-email','inp-area':'inp-area','inp-location':'inp-location',
    'inp-year':'inp-year','inp-coord-img':'inp-coord-img',
    'inp-income':'inp-income','inp-remarks':'inp-remarks',
};

function applyLang(){
    STATIC_IDS.forEach(id=>{
        const el=document.getElementById(id); if(el) el.textContent=T(id);
    });
    Object.entries(PLACEHOLDER_IDS).forEach(([id,key])=>{
        const el=document.getElementById(id); if(el) el.placeholder=T(key);
    });
    const optBrgy=document.getElementById('opt-brgy');
    if(optBrgy) optBrgy.textContent=T('opt-brgy');
    renderGroups();
    rebuildAllFamilyRows();
    // update btn-add-family-lbl
    const bfl = document.getElementById('btn-add-family-lbl');
    if(bfl) bfl.textContent = T('btn-add-family-lbl');
}

/* ─── Nuclear Family system ─── */
let familyCount = 0;

function makeSelOpts(key){
    return T(key).map((lbl,i)=>`<option value="${i}">${lbl}</option>`).join('');
}
// Use label text as the stored value (for employment_status, educational_attainment)
function makeSelOptsLabels(key){
    return T(key).map(lbl=>`<option value="${lbl}">${lbl}</option>`).join('');
}

function familyMemberTableHTML(fi){
    return `<div class="member-table-wrap" style="overflow-x:auto;margin-top:8px;">
        <table class="member-table" id="fam_table_${fi}">
            <thead><tr>
                <th>#</th>
                <th class="th-name-h">Name</th>
                <th>Relationship</th>
                <th>Head</th>
                <th class="th-age-h">Age</th>
                <th class="th-bdate-h">Birthdate</th>
                <th class="th-sex-h">Sex</th>
                <th>LGBTQIA+</th>
                <th class="th-civil-h">Civil Status</th>
                <th class="th-vuln-h">Vulnerable Sector</th>
                <th class="th-employ-h">Employment Status</th>
                <th class="th-educ-h">Educational Attainment</th>
                <th></th>
            </tr></thead>
            <tbody id="fam_body_${fi}"></tbody>
        </table>
    </div>
    <button type="button" class="btn-add-member" onclick="addFamilyMemberRow(${fi})" style="margin-top:10px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        <span class="btn-add-lbl-s">${T('btn-add-lbl')}</span>
    </button>`;
}


function addNuclearFamily(){
    familyCount++;
    const fi = familyCount;
    const stripeN = ((fi-1)%6)+1;
    const container = document.getElementById('familyList');
    const card = document.createElement('div');
    card.className = 'nf-card';
    card.id = `nf_${fi}`;
    card.innerHTML = `
        <div class="nf-card-header">
            <div class="nf-toggle-area" onclick="toggleFamily(${fi})">
                <div class="nf-stripe nf-stripe-${stripeN}"></div>
                <span class="nf-num">${fi}</span>
                <div class="nf-header-text">
                    <div class="nf-label" id="nf_lbl_${fi}">${T('family-lbl')} ${fi}</div>
                    <div class="nf-sublabel" id="nf_sub_${fi}">${T('lbl-family-head')}: —</div>
                </div>
                <div class="nf-pills">
                    <span class="nf-type-badge" id="nf_badge_${fi}">—</span>
                    <span class="nf-count-badge" id="nf_cnt_${fi}">0 members</span>
                </div>
                <svg class="nf-toggle" id="nf_tog_${fi}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
            <button type="button" class="nf-remove-btn" onclick="removeFamily(${fi})">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                Remove
            </button>
        </div>
        <div class="nf-body" id="nf_body_${fi}">
            <div class="nf-body-inner">
                <div class="nf-meta">
                    <div class="form-group">
                        <label class="form-label">
                            <span class="lbl-family-name-s">${T('lbl-family-name')}</span>
                            ${fi === 1 ? '<span style="font-size:10px;font-weight:400;color:var(--gray-400);text-transform:none;letter-spacing:0;margin-left:4px;">(auto-filled)</span>' : '<span class="req">*</span>'}
                        </label>
                        {{-- DB: nuclear_families.family_name --}}
                        <input type="text" class="form-input" name="fam[${fi}][family_name]"
                            id="nf_fname_${fi}"
                            style="font-size:12px;padding:6px 10px;${fi === 1 ? 'background:var(--gray-50);color:var(--gray-600);' : ''}"
                            placeholder="${fi === 1 ? 'Auto-filled from household head' : 'e.g. Dela Cruz'}"
                            ${fi === 1 ? 'readonly' : ''}
                            oninput="${fi !== 1 ? `updateFamilyLabel(${fi},this.value)` : ''}">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><span class="lbl-family-type-s">${T('lbl-family-type')}</span></label>
                        {{-- DB: nuclear_families.family_type --}}
                        <select class="form-select" name="fam[${fi}][family_type]" style="font-size:12px;padding:6px 10px;" onchange="updateFamilyTypeBadge(this,${fi})">
                            <option value="">${T('opt-select')}</option>${T('sel-famtype').map((lbl,i)=>`<option value="${i}">${lbl}</option>`).join('')}
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            <span class="lbl-family-head-s">${T('lbl-family-head')}</span>
                            ${fi === 1 ? '<span style="font-size:10px;font-weight:400;color:var(--gray-400);text-transform:none;letter-spacing:0;margin-left:4px;">(auto-filled)</span>' : ''}
                        </label>
                        {{-- DB: nuclear_families.family_head --}}
                        <input type="text" class="form-input" name="fam[${fi}][family_head]"
                            id="nf_fhead_${fi}"
                            style="font-size:12px;padding:6px 10px;${fi === 1 ? 'background:var(--gray-50);color:var(--gray-600);' : ''}"
                            placeholder="${fi === 1 ? 'Auto-filled from household head name' : 'Full name of head'}"
                            ${fi === 1 ? 'readonly' : ''}
                            oninput="${fi !== 1 ? `updateFamilyHead(${fi},this.value)` : ''}">
                    </div>
                </div>

                ${fi === 1 ? headInfoPanelHTML(fi) : ''}

                <div class="nf-members-header">
                    <div class="nf-members-title">
                        <span class="fsn" style="width:20px;height:20px;font-size:10px;">${fi}</span>
                        <span class="members-title-s">${T('members-title')}</span>
                    </div>
                </div>
                ${familyMemberTableHTML(fi)}
            </div>
        </div>`;
    container.appendChild(card);
    if(fi === 1){
        // NF1: head panel above already covers member row 1 (fam[1][m][1][...]).
        // Pre-set the counter so the next "Add Member" button starts at row 2.
        famMemberCount[fi] = 1;
    } else {
        addFamilyMemberRow(fi);
    }
    updateResidentsCount();
    // scroll into view smoothly
    if(!_pageInitialising) setTimeout(()=>card.scrollIntoView({behavior:'smooth',block:'nearest'}),50);
}

function toggleFamily(fi){
    const body = document.getElementById(`nf_body_${fi}`);
    const tog  = document.getElementById(`nf_tog_${fi}`);
    body.classList.toggle('nf-collapsed');
    tog.classList.toggle('collapsed');
}

function updateFamilyLabel(fi, val){
    const lbl = document.getElementById(`nf_lbl_${fi}`);
    if(lbl) lbl.textContent = val.trim() ? `${T('family-lbl')} ${fi} — ${val.trim()}` : `${T('family-lbl')} ${fi}`;
}

/* ── Head info panel HTML (Nuclear Family 1 only) ── */
function headInfoPanelHTML(fi){
    const sp=T('opt-select'), sm=T('opt-male'), sf=T('opt-female');
    const civil=makeSelOpts('sel-civil'), vuln=makeSelOpts('sel-vuln'),
          emp=makeSelOptsLabels('sel-employ'), educ=makeSelOptsLabels('sel-educ');
    const espPh=T('employ-spec-ph'), otherPh=T('employ-other-ph');
    return `
    <div id="head_panel_${fi}" style="background:var(--blue-pale);border:1px solid #c3d8f5;border-radius:6px;padding:14px 16px;margin-bottom:16px;">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--blue);margin-bottom:12px;display:flex;align-items:center;gap:6px;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Household Head Details
        </div>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;">
            <input type="hidden" name="fam[${fi}][m][1][is_family_head]" value="1">
            <div class="form-group" style="grid-column:span 2;">
                <label class="form-label" style="font-size:10px;">Full Name <span class="req">*</span></label>
                <input type="text" class="form-input" name="fam[${fi}][m][1][full_name]"
                    style="font-size:12px;padding:6px 8px;"
                    id="hp_name_${fi}"
                    oninput="syncHeadNameToSection1(this.value)">
            </div>
            <div class="form-group">
                <label class="form-label" style="font-size:10px;">Age</label>
                <input type="number" class="form-input" name="fam[${fi}][m][1][age_display]" min="0" max="120"
                    style="font-size:12px;padding:6px 8px;background:var(--gray-50);color:var(--gray-600);"
                    title="Auto-calculated from Birthdate" readonly tabindex="-1"
                    id="hp_age_${fi}">
            </div>
            <div class="form-group" style="grid-column:span 2;">
                <label class="form-label" style="font-size:10px;">Birthdate</label>
                <input type="date" class="form-input" name="fam[${fi}][m][1][birthday]"
                    style="font-size:12px;padding:6px 8px;"
                    id="hp_bday_${fi}"
                    oninput="calcAge(this,'hp_age_${fi}')">
            </div>
            <div class="form-group">
                <label class="form-label" style="font-size:10px;">Sex</label>
                <select class="form-select" name="fam[${fi}][m][1][sex]"
                    style="font-size:12px;padding:6px 8px;"
                    id="hp_sex_${fi}">
                    <option value="">${sp}</option>
                    <option value="Male">${sm}</option>
                    <option value="Female">${sf}</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" style="font-size:10px;">Civil Status</label>
                <select class="form-select" name="fam[${fi}][m][1][civil_status]"
                    style="font-size:12px;padding:6px 8px;"
                    id="hp_civil_${fi}">
                    <option value="">${sp}</option>
                    <option value="Single">Single</option>
                    <option value="Married">Married</option>
                    <option value="Legally Separated">Legally Separated</option>
                    <option value="Widowed">Widowed</option>
                </select>
            </div>
            <div class="form-group" style="display:flex;flex-direction:column;justify-content:flex-end;padding-bottom:4px;">
                <label class="form-label" style="font-size:10px;">LGBTQIA+</label>
                <div style="padding:8px 0;">
                    <input type="checkbox" name="fam[${fi}][m][1][is_lgbtqia]" value="1"
                        style="accent-color:var(--blue);width:16px;height:16px;"
                        id="hp_lgbt_${fi}">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" style="font-size:10px;">Vulnerable Sector</label>
                <select class="form-select" name="fam[${fi}][m][1][vuln_sector]"
                    style="font-size:12px;padding:6px 8px;"
                    id="hp_vuln_${fi}"
                    onchange="onVuln(this,'${fi}_1')">
                    <option value="">${sp}</option>${vuln}
                </select>
                <div id="vd_${fi}_1" style="display:none;margin-top:5px;font-size:11px;"></div>
            </div>
            <div class="form-group" style="grid-column:span 2;">
                <label class="form-label" style="font-size:10px;">Employment Status</label>
                <select class="form-select" name="fam[${fi}][m][1][employment_status]"
                    style="font-size:12px;padding:6px 8px;"
                    id="hp_emp_${fi}"
                    onchange="onEmp(this,'${fi}_1')">
                    <option value="">${sp}</option>${emp}
                </select>
                <div id="ed_${fi}_1" style="display:none;margin-top:4px;">
                    <input type="text" class="form-input emp-job-input" name="fam[${fi}][m][1][job_title]"
                        placeholder="${espPh}" style="font-size:11px;padding:4px 8px;"
                        id="hp_job_${fi}">
                    <input type="text" class="form-input emp-other-input" name="fam[${fi}][m][1][employment_other]"
                        placeholder="${otherPh}" style="font-size:11px;padding:4px 8px;display:none;">
                </div>
            </div>
            <div class="form-group" style="grid-column:span 2;">
                <label class="form-label" style="font-size:10px;">Educational Attainment</label>
                <select class="form-select" name="fam[${fi}][m][1][educational_attainment]"
                    style="font-size:12px;padding:6px 8px;"
                    id="hp_educ_${fi}">
                    <option value="">${sp}</option>${educ}
                </select>
            </div>
        </div>
    </div>`;
}

/* ── Two-way sync: Section 1 ↔ NF1 family head ──────────────────────
   Section 1 fields:  #inp-hhname, #inp-sex, #inp-birthday, #inp-civil
   NF1 family head:   fam[1][family_head] input
   NF1 head panel:    #hp_sex_1, #hp_bday_1, #hp_civil_1  (inside headInfoPanelHTML)
   Member row 1 name: fam[1][m][1][full_name]  (for NF2+ auto-fill, not NF1)
──────────────────────────────────────────────────────────────────── */

// Section 1 name → NF1 (called when #inp-hhname changes)
// The head panel (fam[1][m][1][...]) is now the source of truth for
// sex / birthday / civil_status — those fields were removed from Section 1.
function syncSection1ToNF1(){
    const name = document.getElementById('inp-hhname')?.value || '';

    // Extract last name (first word before comma, or first word if no comma)
    const lastName = name.includes(',')
        ? name.split(',')[0].trim()
        : name.trim().split(' ')[0];

    // Auto-fill NF1 family_name (last name) and family_head (full name) — both readonly
    const nf1FamilyName = document.getElementById('nf_fname_1');
    const nf1HeadInput  = document.getElementById('nf_fhead_1');

    if(nf1FamilyName) { nf1FamilyName.value = lastName; updateFamilyLabel(1, lastName); }
    if(nf1HeadInput)  { nf1HeadInput.value  = name;     updateFamilyHead(1, name); }

    // Keep head panel full_name in sync too
    const hpName = document.getElementById('hp_name_1');
    if(hpName && hpName.value !== name) hpName.value = name;
}

// Head panel full_name → Section 1 household_head_name + NF1 meta fields
function syncHeadNameToSection1(val){
    const hhName = document.getElementById('inp-hhname');
    if(hhName && hhName.value !== val) hhName.value = val;

    // Re-use syncSection1ToNF1 logic to keep NF1 meta consistent
    syncSection1ToNF1();
}

// NF1 family_head input → Section 1 household_head_name (and subtitle)
function updateFamilyHead(fi, val){
    const sub = document.getElementById(`nf_sub_${fi}`);
    if(sub) sub.textContent = `${T('lbl-family-head')}: ${val.trim() || '—'}`;

    // Auto-fill member row #1 full_name (NF2+ only)
    const firstRow = document.getElementById(`fmr_${fi}_1`);
    if(firstRow){
        const nameInput = firstRow.querySelector(`input[name="fam[${fi}][m][1][full_name]"]`);
        if(nameInput) nameInput.value = val;
    }

    // Sync back to Section 1 household_head_name (NF1 only)
    if(fi === 1){
        const hhName = document.getElementById('inp-hhname');
        if(hhName) hhName.value = val;
    }
}


function removeFamily(fi){
    if(document.querySelectorAll('.nf-card').length <= 1){
        alert('At least one nuclear family is required.');
        return;
    }
    if(confirm('Remove this nuclear family and all its members?')){
        document.getElementById(`nf_${fi}`)?.remove();
        updateResidentsCount();
    }
}

function updateFamilyTypeBadge(sel, fi){
    const badge = document.getElementById(`nf_badge_${fi}`);
    const opts = T('sel-famtype');
    const idx = parseInt(sel.value);
    badge.textContent = (!isNaN(idx) && opts[idx]) ? opts[idx] : '—';
}

/* per-family member counters */
const famMemberCount = {};

function addFamilyMemberRow(fi){
    if(!famMemberCount[fi]) famMemberCount[fi] = 0;
    famMemberCount[fi]++;
    const mi = famMemberCount[fi];
    const tb = document.getElementById(`fam_body_${fi}`);
    if(!tb) return;
    const tr = document.createElement('tr');
    tr.id = `fmr_${fi}_${mi}`;
    tr.innerHTML = familyMemberRowHTML(fi, mi);
    tb.appendChild(tr);
    updateResidentsCount();
}

function removeFamilyMemberRow(fi, mi){
    const tb = document.getElementById(`fam_body_${fi}`);
    if(tb && tb.children.length <= 1){ alert(T('residents-hint')); return; }
    document.getElementById(`fmr_${fi}_${mi}`)?.remove();
    updateResidentsCount();
}

function updateResidentsCount(){
    let total = 0;
    document.querySelectorAll('[id^="fam_body_"]').forEach(tb => {
        const fi = parseInt(tb.id.replace('fam_body_',''));
        // NF1: head panel = 1 + table rows; others: just table rows
        const panelCount = (fi === 1 && document.getElementById('head_panel_1')) ? 1 : 0;
        const n = tb.children.length + panelCount;
        total += n;
        const badge = document.getElementById(`nf_cnt_${fi}`);
        if(badge) badge.textContent = `${n} member${n!==1?'s':''}`;
    });
    const el = document.getElementById('numResidents');
    if(el) el.value = total || '';
}


function familyMemberRowHTML(fi, mi){
    const sp=T('opt-select'), sm=T('opt-male'), sf=T('opt-female');
    const civil=makeSelOpts('sel-civil'), vuln=makeSelOpts('sel-vuln'),
          emp=makeSelOptsLabels('sel-employ'), educ=makeSelOptsLabels('sel-educ');
    const espPh=T('employ-spec-ph'), namePh=T('th-name'), otherPh=T('employ-other-ph');
    const uid=`${fi}_${mi}`;
    // DB field mapping:
    // fam[fi][m][mi][full_name]       → family_members.full_name
    // fam[fi][m][mi][birthday]        → family_members.birthday  (age is VIRTUAL)
    // fam[fi][m][mi][sex]             → family_members.sex
    // fam[fi][m][mi][civil_status]    → family_members.civil_status (new col)
    // fam[fi][m][mi][relationship]    → family_members.relationship
    // fam[fi][m][mi][is_pwd]          → family_members.is_pwd
    // fam[fi][m][mi][is_student]      → family_members.is_student
    // fam[fi][m][mi][educational_attainment] → family_members.educational_attainment
    // fam[fi][m][mi][vuln_sector]     → family_member_details.vulnerable_sector
    // fam[fi][m][mi][vuln_registered] → family_member_details.vuln_registered
    // fam[fi][m][mi][vuln_id_number]  → family_member_details.vuln_id_number
    // fam[fi][m][mi][is_lgbtqia]      → family_member_details.is_lgbtqia
    // fam[fi][m][mi][employment_status] → family_member_details.employment_status
    // fam[fi][m][mi][job_title]       → family_member_details.job_title
    return `
      <td style="font-weight:700;color:var(--blue);font-size:12px">${mi}</td>
      <td><input type="text" class="form-input" name="fam[${fi}][m][${mi}][full_name]" placeholder="${namePh}" style="min-width:130px;font-size:11px;padding:5px 8px"></td>
      <td><select class="form-select" name="fam[${fi}][m][${mi}][relationship]" style="min-width:110px;font-size:11px;padding:5px 6px">
          <option value="">${sp}</option>
          <option value="Head">Head</option>
          <option value="Spouse">Spouse</option>
          <option value="Son">Son</option>
          <option value="Daughter">Daughter</option>
          <option value="Father">Father</option>
          <option value="Mother">Mother</option>
          <option value="Sibling">Sibling</option>
          <option value="Grandchild">Grandchild</option>
          <option value="Grandparent">Grandparent</option>
          <option value="Uncle/Aunt">Uncle/Aunt</option>
          <option value="Nephew/Niece">Nephew/Niece</option>
          <option value="Cousin">Cousin</option>
          <option value="In-law">In-law</option>
          <option value="Other">Other</option>
      </select></td>
      <td style="text-align:center">
        <input type="checkbox" name="fam[${fi}][m][${mi}][is_family_head]" value="1"
          style="accent-color:#7C3AED;width:16px;height:16px;margin-top:5px"
          title="Check if this member is the head of this nuclear family">
      </td>
      <td><input type="number" class="form-input" id="age_${uid}" name="fam[${fi}][m][${mi}][age_display]" min="0" max="120" style="width:52px;font-size:11px;padding:5px 6px;background:var(--gray-50);color:var(--gray-600);" title="Auto-calculated from Birthdate" readonly tabindex="-1"></td>
      <td><input type="date" class="form-input" name="fam[${fi}][m][${mi}][birthday]" style="min-width:130px;font-size:11px;padding:5px 6px" oninput="calcAge(this,'age_${uid}')"></td>
      <td><select class="form-select" name="fam[${fi}][m][${mi}][sex]" style="min-width:80px;font-size:11px;padding:5px 6px">
          <option value="">${sp}</option><option value="Male">${sm}</option><option value="Female">${sf}</option>
      </select></td>
      <td style="text-align:center"><input type="checkbox" name="fam[${fi}][m][${mi}][is_lgbtqia]" value="1" style="accent-color:var(--blue);width:16px;height:16px;margin-top:5px"></td>
      <td><select class="form-select" name="fam[${fi}][m][${mi}][civil_status]" style="min-width:110px;font-size:11px;padding:5px 6px">
          <option value="">${sp}</option>${civil}</select></td>
      <td>
        <select class="form-select" name="fam[${fi}][m][${mi}][vuln_sector]" style="min-width:135px;font-size:11px;padding:5px 6px" onchange="onVuln(this,'${uid}')">
            <option value="">${sp}</option>${vuln}</select>
        <div id="vd_${uid}" style="display:none;margin-top:5px;font-size:11px"></div>
      </td>
      <td>
        <select class="form-select" name="fam[${fi}][m][${mi}][employment_status]" style="min-width:135px;font-size:11px;padding:5px 6px" onchange="onEmp(this,'${uid}')">
            <option value="">${sp}</option>${emp}</select>
        <div id="ed_${uid}" style="display:none;margin-top:4px">
            <input type="text" class="form-input emp-job-input" name="fam[${fi}][m][${mi}][job_title]" placeholder="${espPh}" style="font-size:11px;padding:4px 8px">
            <input type="text" class="form-input emp-other-input" name="fam[${fi}][m][${mi}][employment_other]" placeholder="${otherPh}" style="font-size:11px;padding:4px 8px;display:none">
        </div>
      </td>
      <td><select class="form-select" name="fam[${fi}][m][${mi}][educational_attainment]" style="min-width:145px;font-size:11px;padding:5px 6px">
          <option value="">${sp}</option>${educ}</select></td>
      <td><button type="button" class="btn-remove" onclick="removeFamilyMemberRow(${fi},${mi})" title="Remove">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
      </button></td>`;
}

function rebuildAllFamilyRows(){
    document.querySelectorAll('[id^="fam_body_"]').forEach(tb=>{
        const fi = tb.id.replace('fam_body_','');
        tb.querySelectorAll('tr').forEach(tr=>{
            const parts = tr.id.replace('fmr_','').split('_');
            const mi = parts[1];
            if(!mi) return;
            // NF1 row 1 fields live in the head panel — skip rebuilding that row in the table
            if(fi === '1' && mi === '1') return;
            const saved={};
            tr.querySelectorAll('[name]').forEach(el=>saved[el.name]=el.type==='checkbox'?el.checked:el.value);
            tr.innerHTML=familyMemberRowHTML(fi,mi);
            tr.querySelectorAll('[name]').forEach(el=>{
                if(saved[el.name]!==undefined){
                    if(el.type==='checkbox') el.checked=saved[el.name];
                    else el.value=saved[el.name];
                }
            });
            // Re-trigger onEmp and onVuln so sub-fields show correctly after rebuild
            const empSel  = tr.querySelector(`[name="fam[${fi}][m][${mi}][employment_status]"]`);
            const vulnSel = tr.querySelector(`[name="fam[${fi}][m][${mi}][vuln_sector]"]`);
            if(empSel)  onEmp(empSel,  `${fi}_${mi}`);
            if(vulnSel) onVuln(vulnSel,`${fi}_${mi}`);
        });
    });
}

/* ─── keep old onVuln/onEmp working with string id ─── */
function onVuln(sel,id){
    // id is fi_mi e.g. "1_2"
    // DB: family_member_details.vuln_registered  → name="fam[fi][m][mi][vuln_registered]"
    // DB: family_member_details.vuln_id_number   → name="fam[fi][m][mi][vuln_id_number]"
    const div=document.getElementById(`vd_${id}`);
    const idx=parseInt(sel.value);
    const parts=id.split('_'); const fi=parts[0]; const mi=parts[1];
    if(idx>=1 && idx<=3){
        div.style.display='block';
        div.innerHTML=`
          <label style="display:flex;align-items:center;gap:5px;margin-bottom:4px">
            <input type="radio" name="fam[${fi}][m][${mi}][vuln_registered]" value="1" style="accent-color:var(--blue)">
            <span>${T('vuln-reg')}</span>
            <input type="text" class="id-input" name="fam[${fi}][m][${mi}][vuln_id_number]" placeholder="${T('vuln-id-ph')}">
          </label>
          <label style="display:flex;align-items:center;gap:5px">
            <input type="radio" name="fam[${fi}][m][${mi}][vuln_registered]" value="0" style="accent-color:var(--blue)">
            <span>${T('vuln-unreg')}</span>
          </label>`;
    } else if(idx===4){
        div.style.display='block';
        div.innerHTML=`<input type="text" class="id-input" name="fam[${fi}][m][${mi}][vuln_id_number]" placeholder="${T('vuln-hh-id')}" style="width:100%">`;
    } else {
        div.style.display='none'; div.innerHTML='';
    }
}

function onEmp(sel,id){
    const div = document.getElementById(`ed_${id}`);
    const v = sel.value;
    // employment_status now stores the label text as value
    const showJob   = ['Employed – specify job','Part-time','Full-time','Self-employed','Freelance',
                       'May Trabaho – tukuyin ang trabaho','Negosyante/Sariling Trabaho'];
    const showOther = (v === 'Other' || v === 'Iba pa');

    if(showOther){
        div.style.display='block';
        const jobInput   = div.querySelector('.emp-job-input');
        const otherInput = div.querySelector('.emp-other-input');
        if(jobInput)   jobInput.style.display   = 'none';
        if(otherInput) otherInput.style.display = 'block';
    } else if(showJob.includes(v)){
        div.style.display='block';
        const jobInput   = div.querySelector('.emp-job-input');
        const otherInput = div.querySelector('.emp-other-input');
        if(jobInput)   jobInput.style.display   = 'block';
        if(otherInput) otherInput.style.display = 'none';
    } else {
        div.style.display='none';
    }
}

function syncMemberCount(v){ /* no-op, now auto-counted */ }

function initMemberTable(){
    // Start with one nuclear family by default
    addNuclearFamily();
}


/* ─── Language switcher ─── */
function setLang(lang){
    LANG=lang;
    ['en','tl','mix'].forEach(l=>document.getElementById(`btn-${l}`).classList.toggle('active',l===lang));
    applyLang();
}

/* ─── Age auto-calculator ─── */
function calcAge(bdayInput, ageFieldId){
    const ageEl = document.getElementById(ageFieldId);
    if(!ageEl) return;
    if(!bdayInput.value){ ageEl.value=''; return; }
    const today = new Date();
    const bday  = new Date(bdayInput.value);
    let age = today.getFullYear() - bday.getFullYear();
    const m = today.getMonth() - bday.getMonth();
    if(m < 0 || (m === 0 && today.getDate() < bday.getDate())) age--;
    ageEl.value = age >= 0 ? age : '';
}

/* ─── Clock ─── */
function pad(n){return String(n).padStart(2,'0');}
function updateClock(){
    const now=new Date();
    const D=['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
    const M=['January','February','March','April','May','June','July','August','September','October','November','December'];
    const MS=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    document.getElementById('top-time').textContent=pad(now.getHours())+':'+pad(now.getMinutes())+':'+pad(now.getSeconds());
    document.getElementById('top-date').textContent=D[now.getDay()]+', '+pad(now.getDate())+' '+MS[now.getMonth()]+' '+now.getFullYear();
    document.getElementById('main-date').textContent=D[now.getDay()]+', '+M[now.getMonth()]+' '+now.getDate()+', '+now.getFullYear();
}
updateClock(); setInterval(updateClock,1000);
document.getElementById('footer-year').textContent=new Date().getFullYear();

/* ─── Sidebar ─── */
function openSidebar(){document.getElementById('sidebar').classList.add('open');document.getElementById('sidebarOverlay').classList.add('active');document.body.style.overflow='hidden';}
function closeSidebar(){document.getElementById('sidebar').classList.remove('open');document.getElementById('sidebarOverlay').classList.remove('active');document.body.style.overflow='';}
document.addEventListener('keydown',e=>{if(e.key==='Escape')closeSidebar();});

/* ─── Submit ─── */
function handleSubmit(e){
    e.preventDefault();
    const lbl=document.getElementById('btn-save-lbl');
    const btn=document.getElementById('btn-save');
    btn.disabled=true;
    lbl.textContent='⏳ Saving...';
    btn.style.background='var(--blue-dark)';
    document.getElementById('householdForm').submit();
}

function confirmReset(){
    if(confirm(T('confirm-reset'))){
        document.getElementById('householdForm').reset();
        document.getElementById('familyList').innerHTML='';
        familyCount=0;
        Object.keys(famMemberCount).forEach(k=>delete famMemberCount[k]);
        initMemberTable();
        renderGroups();
    }
}

/* ─── Init ─── */
// Force scroll to top on every page load/reload
if('scrollRestoration' in history) history.scrollRestoration = 'manual';
window.scrollTo(0, 0);

let _pageInitialising = true;
initMemberTable();
applyLang();
// Allow scrollIntoView again after init is fully done
requestAnimationFrame(()=>{ _pageInitialising = false; window.scrollTo(0,0); });

// On load: if old() values exist (after validation fail), push Section 1 → NF1
(function(){
    const name = document.getElementById('inp-hhname')?.value;
    if(name) syncSection1ToNF1();
})();
</script>
</body>
</html>