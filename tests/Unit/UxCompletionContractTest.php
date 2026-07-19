<?php
namespace Modules\Glitter\CommunityIntelligence\Tests\Unit;
use PHPUnit\Framework\TestCase;
class UxCompletionContractTest extends TestCase
{
    private function layout(string $name): string { return file_get_contents(dirname(__DIR__, 2).'/resources/layouts/admin/'.$name.'.json'); }
    public function test_briefing_renders_statistics_without_raw_json(): void {
        $layout=$this->layout('admin_community_intelligence_briefing');
        self::assertStringContainsString('Object.entries(briefing?.data?.by_rule ?? {})',$layout);
        self::assertStringContainsString("['high', 'medium', 'low'].map",$layout);
        self::assertStringContainsString('rule_stats_empty',$layout);
        self::assertStringContainsString('priority_stats_empty',$layout);
        self::assertStringNotContainsString('JSON.stringify',$layout);
        self::assertStringNotContainsString('"name": "Pre"',$layout);
    }
    public function test_collections_use_supported_iteration_and_separate_errors(): void {
        foreach(glob(dirname(__DIR__,2).'/resources/layouts/admin/*.json') as $path){$raw=file_get_contents($path);self::assertStringNotContainsString('"iteration": {"data"',$raw,basename($path));}
        foreach(['signals'=>'signals','incidents'=>'incidents','audit'=>'audits','settings'=>'rules'] as $name=>$source){$raw=$this->layout('admin_community_intelligence_'.$name);self::assertStringContainsString('!_dataSourceErrors?.'.$source,$raw,$name);self::assertStringNotContainsString('"fallback"',$raw,$name);}
    }
    public function test_detail_supports_review_sensitive_and_safe_timeline(): void {
        $detail=$this->layout('admin_community_intelligence_signal_detail');
        foreach(['reviewing','observing','resolved','dismissed','reason_code','note','view_sensitive','refetchDataSource'] as $term){self::assertStringContainsString($term,$detail);}
        self::assertStringContainsString('"id": "sensitive"', $detail);
        self::assertStringContainsString('"auto_fetch": false', $detail);
        self::assertStringNotContainsString('JSON.stringify',$detail);
        self::assertStringNotContainsString('JSON.stringify',$this->layout('admin_community_intelligence_incident_detail'));
    }
}
