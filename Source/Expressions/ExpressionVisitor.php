<?php

namespace Pinq\Expressions;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ExpressionVisitor extends ExpressionWalker
{
    final public function WalkArray(ArrayExpression $Expression)
    {
        $this->VisitArray($Expression);
        return parent::WalkArray($Expression);
    }
    protected function VisitArray(ArrayExpression $Expression) {}

    final public function WalkAssignment(AssignmentExpression $Expression)
    {
        $this->VisitArray($Expression);
        return parent::WalkAssignment($Expression);
    }
    protected function VisitAssignment(AssignmentExpression $Expression) {}

    final public function WalkBinaryOperation(BinaryOperationExpression $Expression)
    {
        $this->VisitBinaryOperation($Expression);
        return parent::WalkBinaryOperation($Expression);
    }
    protected function VisitBinaryOperation(BinaryOperationExpression $Expression) {}

    final public function WalkCast(CastExpression $Expression)
    {
        $this->VisitCast($Expression);
        return parent::WalkCast($Expression);
    }
    protected function VisitCast(CastExpression $Expression) {}

    final public function WalkClosure(ClosureExpression $Expression)
    {
        $this->VisitClosure($Expression);
        return parent::WalkClosure($Expression);
    }
    protected function VisitClosure(ClosureExpression $Expression) {}

    final public function WalkEmpty(EmptyExpression $Expression)
    {
        $this->VisitEmpty($Expression);
        return parent::WalkEmpty($Expression);
    }
    protected function VisitEmpty(EmptyExpression $Expression) {}

    final public function WalkField(FieldExpression $Expression)
    {
        $this->VisitField($Expression);
        return parent::WalkField($Expression);
    }
    protected function VisitField(FieldExpression $Expression) {}

    final public function WalkFunctionCall(FunctionCallExpression $Expression)
    {
        $this->VisitFunctionCall($Expression);
        return parent::WalkFunctionCall($Expression);
    }
    protected function VisitFunctionCall(FunctionCallExpression $Expression) {}

    final public function WalkIndex(IndexExpression $Expression)
    {
        $this->VisitIndex($Expression);
        return parent::WalkIndex($Expression);
    }
    protected function VisitIndex(IndexExpression $Expression) {}

    final public function WalkInvocation(InvocationExpression $Expression)
    {
        $this->VisitInvocation($Expression);
        return parent::WalkInvocation($Expression);
    }
    protected function VisitInvocation(InvocationExpression $Expression) {}

    final public function WalkMethodCall(MethodCallExpression $Expression)
    {
        $this->VisitMethodCall($Expression);
        return parent::WalkMethodCall($Expression);
    }
    protected function VisitMethodCall(MethodCallExpression $Expression) {}

    final public function WalkNew(NewExpression $Expression)
    {
        $this->VisitNew($Expression);
        return parent::WalkNew($Expression);
    }
    protected function VisitNew(NewExpression $Expression) {}

    final public function WalkReturn(ReturnExpression $Expression)
    {
        $this->VisitReturn($Expression);
        return parent::WalkReturn($Expression);
    }
    protected function VisitReturn(ReturnExpression $Expression) {}

    final public function WalkStaticMethodCall(StaticMethodCallExpression $Expression)
    {
        $this->VisitStaticMethodCall($Expression);
        return parent::WalkStaticMethodCall($Expression);
    }
    protected function VisitStaticMethodCall(StaticMethodCallExpression $Expression) {}

    final public function WalkTernary(TernaryExpression $Expression)
    {
        $this->VisitTernary($Expression);
        return parent::WalkTernary($Expression);
    }
    protected function VisitTernary(TernaryExpression $Expression) {}

    final public function WalkUnaryOperation(UnaryOperationExpression $Expression)
    {
        $this->VisitUnaryOperation($Expression);
        return parent::WalkUnaryOperation($Expression);
    }
    protected function VisitUnaryOperation(UnaryOperationExpression $Expression) {}

    final public function WalkValue(ValueExpression $Expression)
    {
        $this->VisitValue($Expression);
        return parent::WalkValue($Expression);
    }
    protected function VisitValue(ValueExpression $Expression) {}

    final public function WalkVariable(VariableExpression $Expression)
    {
        $this->VisitVariable($Expression);
        return parent::WalkVariable($Expression);
    }
    protected function VisitVariable(VariableExpression $Expression) {}

}