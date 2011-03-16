<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
 <xsl:param name="owner" select="'Nicolas Eliaszewicz'"/>
 <xsl:output method="html" encoding="iso-8859-1" indent="no"/>
 
 
 <xsl:template match="//registrationreport">
    <xsl:apply-templates/>
 </xsl:template>


<xsl:template name="makeAggregate">
    <xsl:param name="aggName"/>
    <xsl:param name="aggPosition"/>    
    <div>
        <xsl:attribute name="class">
            <xsl:value-of select="$aggName" />
        </xsl:attribute>
        <span class="detailsLabel">
            <xsl:value-of select="$aggName" />
        </span>
        <div >
            <xsl:attribute name='id'>
                <xsl:value-of select="$aggName" />Data<xsl:value-of select="$aggPosition"/>
            </xsl:attribute>    
            <xsl:apply-templates/>
        </div>
    </div>
</xsl:template>
<xsl:template name="makeProperty">
    <xsl:param name="propName"/>
    <xsl:param name="propValue"/>
    <xsl:param name="propTitle"/>
    <div>
        <xsl:attribute name="class">
            <xsl:value-of select="$propName" />
        </xsl:attribute>
        <span class="detailsLabel">
            <xsl:value-of select="$propTitle" />
        </span>
        <xsl:value-of select="$propValue"/>
    </div>
</xsl:template>

<!--********** Aggregate   ***************-->
<xsl:template match="activity"><xsl:call-template name="makeAggregate"><xsl:with-param name="aggName" select="name()"/><xsl:with-param name="aggPosition" select="position()"/></xsl:call-template></xsl:template>
<xsl:template match="children"><xsl:call-template name="makeAggregate"><xsl:with-param name="aggName" select="name()"/><xsl:with-param name="aggPosition" select="position()"/></xsl:call-template></xsl:template>
<xsl:template match="objectives"><xsl:call-template name="makeAggregate"><xsl:with-param name="aggName" select="name()"/><xsl:with-param name="aggPosition" select="position()"/></xsl:call-template></xsl:template>
<xsl:template match="objective"><xsl:call-template name="makeAggregate"><xsl:with-param name="aggName" select="name()"/><xsl:with-param name="aggPosition" select="position()"/></xsl:call-template></xsl:template>
<xsl:template match="runtime"><xsl:call-template name="makeAggregate"><xsl:with-param name="aggName" select="name()"/><xsl:with-param name="aggPosition" select="position()"/></xsl:call-template></xsl:template>
<xsl:template match="interactions"><xsl:call-template name="makeAggregate"><xsl:with-param name="aggName" select="name()"/><xsl:with-param name="aggPosition" select="position()"/></xsl:call-template></xsl:template>
<xsl:template match="interaction"><xsl:call-template name="makeAggregate"><xsl:with-param name="aggName" select="name()"/><xsl:with-param name="aggPosition" select="position()"/></xsl:call-template></xsl:template>
<xsl:template match="static"><xsl:call-template name="makeAggregate"><xsl:with-param name="aggName" select="name()"/><xsl:with-param name="aggPosition" select="position()"/></xsl:call-template></xsl:template>
<xsl:template match="learnerpreference"><xsl:call-template name="makeAggregate"><xsl:with-param name="aggName" select="name()"/><xsl:with-param name="aggPosition" select="position()"/></xsl:call-template></xsl:template>
<xsl:template match="comments_from_learner"> <div class='comments_from_learner' > <span class="detailsLabel">comments_from_learner</span>  <xsl:apply-templates/></div> </xsl:template>
<xsl:template match="comment"><xsl:call-template name="makeAggregate"><xsl:with-param name="aggName" select="name()"/><xsl:with-param name="aggPosition" select="position()"/></xsl:call-template></xsl:template>
<xsl:template match="comments_from_lms"><xsl:call-template name="makeAggregate"><xsl:with-param name="aggName" select="name()"/><xsl:with-param name="aggPosition" select="position()"/></xsl:call-template></xsl:template>
<xsl:template match="correct_responses"><xsl:call-template name="makeAggregate"><xsl:with-param name="aggName" select="name()"/><xsl:with-param name="aggPosition" select="position()"/></xsl:call-template></xsl:template>


<!--  Property field templates -->

<!--  Activity Level -->
<xsl:template match="title"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="satisfied"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="completed"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="progressstatus"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="attemptprogressstatus"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="attempts"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="suspended"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>

<!--  Objective Level -->
<xsl:template match="measurestatus"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="normalizedmeasure"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="progressstatus"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="satisfiedstatus"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>

<!-- Runtime Level -->
<xsl:template match="completion_status"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="credit"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="entry"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="exit"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="audio_level"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="language"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="delivery_speed"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="audio_captioning"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="location"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="mode"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="progress_measure"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="score_scaled"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="score_raw"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="score_min"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="score_max"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="total_time"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="timetracked"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="success_status"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="suspend_data"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="value"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="location"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="date_time"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="timestamp"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="response"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="weighting"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="learner_response"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="result"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="latency"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="description"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="completion_threshold"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="launch_data"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="learner_id"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="learner_name"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="max_time_allowed"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="scaled_passing_score"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>
<xsl:template match="time_limit_action"><xsl:call-template name="makeProperty"><xsl:with-param name="propName" select="name()"/><xsl:with-param name="propValue" select="."/><xsl:with-param name="propTitle" select="name()"/></xsl:call-template></xsl:template>

<!-- ***********  Template Maker (old)**************
 <xsl:template match="*">
    &lt;xsl:template match="<xsl:value-of select="name()"/>"&gt;
        &lt;div class='<xsl:value-of select="name()"/>' &gt;
            &lt;span class="detailsLabel"&gt;<xsl:value-of select="name()"/>&lt;/span&gt;
            &lt;xsl:value-of select="."/&gt;
        &lt;/div&gt;
    &lt;/xsl:template&gt;<br/>
    <xsl:apply-templates/>
</xsl:template>
 ***********************************************-->

<!-- ***********  CSS Maker **************
 <xsl:template match="*">
    <xsl:value-of select="name()"/> {}<br/>
        
    <xsl:apply-templates/>
</xsl:template>
 ***********************************************-->


</xsl:stylesheet>