import test from 'node:test';
import assert from 'node:assert/strict';
import * as monitoring from './monitoring.js';
test('date filters include the entire final day', () => {
 const params = monitoring.rangeParams('2', '2026-09-09', '2026-09-09');
 assert.equal(params.get('from'), '2026-09-09 00:00:00');
 assert.equal(params.get('to'), '2026-09-09 23:59:59.999999');
 assert.equal(params.get('device_id'), '2');
 assert.throws(() => monitoring.rangeParams('2', '2026-09-10', '2026-09-09'));
});
test('missing sensor readings are never rendered as zero', () => {
 assert.equal(monitoring.numeric(null), null);
 assert.equal(monitoring.numeric(''), null);
 assert.equal(monitoring.numeric('2.5300'), 2.53);
 assert.equal(monitoring.numeric('broken'), null);
});
test('live chart ignores duplicate and older readings and bounds its history', () => {
 const points = [];
 for(let i=0;i<40;i++) monitoring.appendReading(points, i * 1000, [i, i+1, i+2]);
 monitoring.appendReading(points, 39000, [99,99,99]);
 monitoring.appendReading(points, 38000, [99,99,99]);
 assert.equal(points.length, 30);
 assert.equal(points[0].time, 10000);
 assert.deepEqual(points.at(-1).values, [39,40,41]);
});
test('naive timestamps are interpreted in application timezone, not browser timezone', () => {
 assert.equal(monitoring.timestamp('2026-09-09 23:07:00', '+07:00'), Date.parse('2026-09-09T16:07:00Z'));
 assert.equal(monitoring.timestamp('2026-09-09T23:07:00.000000Z', '+07:00'), Date.parse('2026-09-09T23:07:00Z'));
});
