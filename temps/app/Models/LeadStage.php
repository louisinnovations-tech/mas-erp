<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class LeadStage extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'pipeline_id',
        'created_by',
        'order',
    ];
    public function lead()
    {


        if(Auth::user()->type == 'company'){
            $users = User::where('created_by',Auth::user()->creatorId())->pluck('id')->toArray();

            return Lead::select('leads.*')->join('user_leads', 'user_leads.lead_id', '=', 'leads.id')
            ->whereIn('user_leads.user_id',$users)
            ->where('leads.stage_id', '=', $this->id)->orderBy('leads.order')->distinct()->get();

        }else{

            return Lead::select('leads.*')->join('user_leads', 'user_leads.lead_id', '=', 'leads.id')
            ->where('user_leads.user_id', '=', Auth::user()->id)
            ->where('leads.stage_id', '=', $this->id)->orderBy('leads.order')->get();
        }

    }
}
