<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model {

  /**
   * Table name.
   *
   * @var string
   */
  protected $table = 'participants';


  protected $fillable = [ 'name', 'user_image', 'email', 'nrc_id', 'ethincity', 'education_level',
      'user_gender', 'dob','current_org', 'user_line_phone',
      'user_mobile_phone', 'user_mailing_address',
      'user_biography','payment_type','bank', 'participant_type','participant_id'];

    public function location()
    {
        return $this->belongsToMany('App\Geolocations', 'participants_geolocations', 'participant_id', 'location_id')->withPivot('pace_id')->withTimestamps();
    }

    public function states()
    {
        return $this->belongsToMany('App\States', 'coordinators_states', 'coordinators_id', 'state_id')->withTimestamps();
    }

  public function districts()
  {
    return $this->belongsToMany('App\Districts', 'coordinators_regions', 'coordinators_id', 'region_id')->withTimestamps();
  }
    public function townships()
    {
        return $this->belongsToMany('App\Townships', 'spotcheckers_townships', 'spotcheckers_id', 'townships_id')->withTimestamps();
    }
  public function villages()
  {
    return $this->belongsToMany('App\Villages', 'enumerators_villages', 'enumerators_id')->withTimestamps();
  }

  public function pgroups()
  {
      return $this->belongsToMany('App\PGroups', 'participants_pgroups', 'participant_id', 'pgroups_id')->withTimestamps();
  }

  public function type()
  {
      return $this->belongsTo('App\ParticipantType', 'participant_id');
  }

    public function parents()
    {
        return $this->belongsToMany('App\Participant', 'participants_relations', 'child_id', 'parent_id');
    }

    public function children()
    {
        return $this->belongsToMany('App\Participant', 'participants_relations', 'parent_id', 'child_id');
    }

    public function answers(){
        return $this->hasMany('App\EmsQuestionsAnswers', 'interviewer_id', 'participant_id');
    }

    public function scopeCoordinator($query)
    {
        return $query->whereParticipantType('coordinator');
    }

    public function scopeEnumerator($query)
    {
        return $query->whereParticipantType('enumerator');
    }

    public function scopeSpotchecker($query)
    {
        return $query->whereParticipantType('spotchecker');
    }

    public function setDobAttribute($date)
    {
        $this->attributes['dob'] = date('Y-m-d', strtotime($date));
    }

    public function getDobAttribute($date)
    {
        // return $value;
        return date('d-m-Y', strtotime($date));
    }

    protected function setNrcIdAttribute($nrc_id)
    {
        $nrc_id = preg_replace('/\s+/', '', $nrc_id);

        $nrc_id = strtolower($nrc_id);

        $pattern = '/(\d+){1,2}\/(\w+a|ah)(\w+a|ah)(\w+a|ah)\((\w)(\w+)?\)(\d+)/i';
        $nrc_id_format =  preg_replace_callback($pattern, function($matches){
            return $matches[1]."/".ucwords($matches[2]).ucwords($matches[3]).ucwords($matches[4])."(".ucwords($matches[5]).")".$matches[7];
        }, $nrc_id);
        $this->attributes['nrc_id'] = $nrc_id_format;
    }

}
