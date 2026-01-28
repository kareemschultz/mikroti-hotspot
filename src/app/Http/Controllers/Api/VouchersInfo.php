<?php

namespace App\Http\Controllers\Api;

use Validator;
use App\Traits\RadiusHelper;
use Illuminate\Http\Request;
use App\Models\HotspotProfile;
use App\Models\HotspotVouchers;
use App\Models\VoucherTemplate;
use App\Http\Controllers\Controller;

class VouchersInfo extends Controller
{
    //

    use RadiusHelper;

    public function print()
    {
        // $sessionToken = request()->get('sessionToken') ?? auth()->user()->sessionToken;
        $apiSecret  =   request()->get('secret');
        $batchCode  =   request()->get('batch');
        $vouhcerId  =   request()->get('id');

        $vouchers = HotspotVouchers::query()
        ->leftJoin('hotspot_profiles','hotspot_profiles.id','hotspot_vouchers.hotspot_profile_id')
        ->leftJoin('resellers','resellers.id','hotspot_vouchers.reseller_id')
        ->select(
            '*',
            'hotspot_vouchers.id as prof_id',
            'hotspot_profiles.name as prof_name',
            'hotspot_profiles.description as prof_desc',
            'hotspot_profiles.price as prof_price',
            'hotspot_profiles.uptime_limit as prof_time_limit',
            'hotspot_profiles.data_limit as prof_data_limit',
            'hotspot_profiles.max_download as prof_max_download',
            'hotspot_profiles.max_upload as prof_max_upload',
            'hotspot_profiles.validity as prof_validity',
            'resellers.name as reseller_name',
            'resellers.id as reseller_id',
        )
        ->where('hotspot_vouchers.used_date',null);


        if($batchCode != null) {
            $vouchers = $vouchers->where('hotspot_vouchers.batch_code',$batchCode);
        }

        
        $vouchers = $vouchers->get();

        $result = [];

        foreach($vouchers as $voucher) {
            $result[] = [
                'id'                    =>  $voucher->prof_id,
                'reseller_name'         =>  $voucher->reseller_name,
                'reseller_id'           =>  $voucher->reseller_id,
                'code'                  =>  $voucher->code,
                'password'              =>  $voucher->password,
                'description'           =>  $voucher->prof_desc,
                'profile'               =>  $voucher->prof_name,
                'price'                 =>  number_format($voucher->price,2),
                'time_limit'            =>  $this->convertSeconds($voucher->prof_time_limit),
                'data_limit'            =>  $voucher->prof_data_limit ? "{$this->convertBytes($voucher->prof_data_limit)}" : "Unlimited",
                'speed_max_download'    =>  $voucher->prof_max_download ? "{$this->convertBytes($voucher->prof_max_download)}ps" : "Unlimited",
                'speed_max_upload'      =>  $voucher->prof_max_upload ? "{$this->convertBytes($voucher->prof_max_upload)}ps" : "Unlimited",
                'validity'              =>  $voucher->prof_validity ? $this->convertSeconds($voucher->prof_validity) : null
            ];
        }

        return response()->json($result);
    }

    public function template()
    {
        // $sessionToken = request()->get('sessionToken') ?? auth()->user()->sessionToken;
        if(!auth()->check()) {
            abort(404);
        }

        $user = auth()->user();
        
        $templateId = request()->get('template');
        $batchCode = request()->get('batch');

        $bodyHTML = '';
        $styleHTML = '';

        $templates = VoucherTemplate::query()
        ->where([
            'user_id' => $user->id,
            'id' => $templateId
        ])
        ->first();

        if(!$templates) {
            $styleHTML = <<<'HTML'
            * {
                box-sizing: border-box;
            }
            
            body {
                color: #333;
                background-color: #FFFFFF;
                font-size: 12px;
                font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
                margin: 0;
                padding: 10px;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .voucher-card {
                display: inline-block;
                width: 220px;
                margin: 5px;
                border-radius: 12px;
                overflow: hidden;
                box-shadow: 0 2px 8px rgba(0,0,0,0.15);
                background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
                border: 1px solid #dee2e6;
                vertical-align: top;
            }

            .voucher-header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 10px 12px;
                text-align: center;
            }

            .voucher-header .profile-name {
                font-size: 14px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin: 0;
            }

            .voucher-header .price-tag {
                font-size: 20px;
                font-weight: 800;
                margin-top: 4px;
            }

            .voucher-body {
                padding: 12px;
                text-align: center;
            }

            .qr-container {
                background: white;
                padding: 8px;
                border-radius: 8px;
                display: inline-block;
                margin-bottom: 10px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            }

            .qr-container img {
                width: 80px;
                height: 80px;
            }

            .code-section {
                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                border-radius: 8px;
                padding: 10px;
                margin-bottom: 10px;
            }

            .code-label {
                font-size: 9px;
                color: #6c757d;
                text-transform: uppercase;
                letter-spacing: 1px;
                margin-bottom: 4px;
            }

            .code-value {
                font-size: 18px;
                font-weight: 800;
                color: #495057;
                font-family: 'Courier New', monospace;
                letter-spacing: 2px;
            }

            .password-section {
                background: #fff3cd;
                border-radius: 8px;
                padding: 8px;
                margin-bottom: 10px;
                border: 1px dashed #ffc107;
            }

            .password-value {
                font-size: 14px;
                font-weight: 700;
                color: #856404;
                font-family: 'Courier New', monospace;
            }

            .info-grid {
                display: flex;
                flex-wrap: wrap;
                gap: 6px;
            }

            .info-item {
                flex: 1 0 45%;
                background: #f8f9fa;
                border-radius: 6px;
                padding: 6px 8px;
                text-align: center;
            }

            .info-item .label {
                font-size: 8px;
                color: #6c757d;
                text-transform: uppercase;
            }

            .info-item .value {
                font-size: 11px;
                font-weight: 600;
                color: #495057;
            }

            .voucher-footer {
                background: #f8f9fa;
                padding: 8px;
                text-align: center;
                border-top: 1px dashed #dee2e6;
            }

            .voucher-footer small {
                color: #6c757d;
                font-size: 8px;
            }

            @page {
                size: auto;
                margin: 8mm;
            }

            @media print {
                body {
                    padding: 0;
                }
                .voucher-card {
                    page-break-inside: avoid;
                    box-shadow: none;
                    border: 1px solid #000;
                }
            }
        HTML;

        $bodyHTML = <<<'HTML'
            <div class="voucher-card">
                <div class="voucher-header">
                    <div class="profile-name" x-text="voucher.profile"></div>
                    <div class="price-tag">₱<span x-text="voucher.price"></span></div>
                </div>
                
                <div class="voucher-body">
                    <div class="qr-container">
                        <img :src="'https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=' + voucher.code" alt="QR Code" />
                    </div>
                    
                    <div class="code-section">
                        <div class="code-label">Voucher Code</div>
                        <div class="code-value" x-text="voucher.code"></div>
                    </div>
                    
                    <template x-if="voucher.password">
                        <div class="password-section">
                            <div class="code-label">Password</div>
                            <div class="password-value" x-text="voucher.password"></div>
                        </div>
                    </template>
                    
                    <div class="info-grid">
                        <template x-if="voucher.time_limit">
                            <div class="info-item">
                                <div class="label">⏱ Time</div>
                                <div class="value" x-text="voucher.time_limit"></div>
                            </div>
                        </template>
                        
                        <template x-if="voucher.data_limit && voucher.data_limit !== 'Unlimited'">
                            <div class="info-item">
                                <div class="label">📊 Data</div>
                                <div class="value" x-text="voucher.data_limit"></div>
                            </div>
                        </template>
                        
                        <template x-if="voucher.validity">
                            <div class="info-item">
                                <div class="label">📅 Valid For</div>
                                <div class="value" x-text="voucher.validity"></div>
                            </div>
                        </template>
                        
                        <template x-if="voucher.speed_max_download && voucher.speed_max_download !== 'Unlimitedps'">
                            <div class="info-item">
                                <div class="label">⬇ Speed</div>
                                <div class="value" x-text="voucher.speed_max_download"></div>
                            </div>
                        </template>
                    </div>
                </div>
                
                <div class="voucher-footer">
                    <small>Connect to WiFi • Enter code • Enjoy!</small>
                </div>
            </div>
        HTML;
        } else {
            $bodyHTML = $templates->body;
            $styleHTML = $templates->head;
        }


        return view('vouchers.main',[
            'batch'         =>  $batchCode,
            'bodyHTML'    =>  $bodyHTML,
            'styleHTML'     =>  $styleHTML
        ]);
    }

    public function getAllProfiles($apiPublic)
    {
        $dnsUrl = request()->get('dnsUrl') ?? '10.10.10.10';

        $validator = Validator::make([
            'apiPublic' =>  $apiPublic,
        ],[
            'apiPublic' =>  'required'
        ]);

        if($validator->fails()) {
            return response()->json([
                'status'    =>  'error',
                'message'   =>  $validator->messages()->first()
            ]);
        }

        $profiles = HotspotProfile::leftJoin('users','users.id','hotspot_profiles.user_id')
        ->where('users.api_public', $apiPublic)
        ->select(
            'hotspot_profiles.*'
            )
        ->get();

        if($profiles->count() == 0) {
            return response()->json([
                'status'    =>  'error',
                'message'   =>  'No Profiles Available'
            ]);
        }

        $results = [];

        foreach($profiles as $profile) {
            $results[] = [
                'name'          =>  $profile->name,
                'description'   =>  $profile->description,
                'price'         =>  $profile->price,
                'time_limit'    =>  $profile->uptime_limit,
                'data_limit'    =>  $profile->data_limit,
                'validity'      =>  $profile->validity,
                'link'          =>  route('cashless.profile',['publicToken' => $apiPublic, 'profileId' => $profile->id, 'dnsUrl' => $dnsUrl])
            ];
        }

        return response()->json($results);

    }

    public function getResellerProfiles($apiPublic,$resellerId)
    {
        $validator = Validator::make([
            'apiPublic' =>  $apiPublic,
        ],[
            'apiPublic' =>  'required'
        ]);

        if($validator->fails()) {
            return response()->json([
                'status'    =>  'error',
                'message'   =>  $validator->messages()->first()
            ]);
        }

        $profiles = HotspotProfile::leftJoin('users','users.id','hotspot_profiles.uid')
        ->where([
            'users.api_public' => $apiPublic,
            'hotspot_profiles.reseller_id' => $resellerId
            ])
        ->select(
            'hotspot_profiles.*'
            )
        ->get();

        if($profiles->count() == 0) {
            return response()->json([
                'status'    =>  'error',
                'message'   =>  'No Profiles Available'
            ]);
        }

        $results = [];

        foreach($profiles as $profile) {
            $results[] = [
                'name'  =>  $profile->name,
            ];
        }

        return response()->json($results);

    }
}
