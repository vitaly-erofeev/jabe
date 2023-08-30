<?php

$contents = '<?xml version="1.0" encoding="UTF-8"?>
<definitions id="definitions" xmlns:bpmndi="http://www.omg.org/spec/BPMN/20100524/DI" xmlns:omgdc="http://www.omg.org/spec/DD/20100524/DC" xmlns:omgdi="http://www.omg.org/spec/DD/20100524/DI"
             xmlns="http://www.omg.org/spec/BPMN/20100524/MODEL"
             xmlns:extension="http://activiti.org/bpmn"
             targetNamespace="Examples">

    <process id="startTimerEventExampleCycle100" name="Timer start event example" isExecutable="true">

        <startEvent id="theStart">
            <timerEventDefinition>
                <timeCycle>R2/PT1M</timeCycle>
            </timerEventDefinition>
        </startEvent>    

        <sequenceFlow id="flow1" sourceRef="theStart" targetRef="receive"/>

        <receiveTask id="receive"/>

        <sequenceFlow id="flow2" sourceRef="receive" targetRef="theEnd"/>

        <endEvent id="theEnd"/>

    </process>
    <bpmndi:BPMNDiagram id="diagram">
       <bpmndi:BPMNPlane bpmnElement="startTimerEventExampleCycle100" id="startTimerEventExample_di">
          <bpmndi:BPMNShape bpmnElement="theStart" id="theStart_di">
             <omgdc:Bounds height="30.0" width="30.0" x="114.0" y="185.0"/>
          </bpmndi:BPMNShape>
          <bpmndi:BPMNShape bpmnElement="receive" id="timer_di">
             <omgdc:Bounds height="30.0" width="30.0" x="195.0" y="185.0"/>
          </bpmndi:BPMNShape>
          <bpmndi:BPMNShape bpmnElement="theEnd" id="theEnd_di">
             <omgdc:Bounds height="28.0" width="28.0" x="270.0" y="186.0"/>
          </bpmndi:BPMNShape>
          <bpmndi:BPMNEdge bpmnElement="flow1" id="flow1_di">
             <omgdi:waypoint x="144.0" y="200.0"/>
             <omgdi:waypoint x="195.0" y="200.0"/>
          </bpmndi:BPMNEdge>
          <bpmndi:BPMNEdge bpmnElement="flow2" id="flow2_di">
             <omgdi:waypoint x="225.0" y="200.0"/>
             <omgdi:waypoint x="270.0" y="200.0"/>
          </bpmndi:BPMNEdge>
       </bpmndi:BPMNPlane>
    </bpmndi:BPMNDiagram>

</definitions>';

$names = [];
for ($i = 801; $i <= 2000; $i += 1) {
    $name = sprintf('StartTimerEventTest.testCycleWithLimitStartTimerEvent%d.bpmn20.xml', $i);
    file_put_contents($name, str_replace('startTimerEventExampleCycle100', "startTimerEventExampleCycle$i", $contents));
    $names[] = sprintf("tests/Resources/Bpmn/Event/Timer/%s", $name);
}
echo implode('", "', $names), "\n";
