package com.example.mindcare.ui.screens

import androidx.compose.foundation.Canvas
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.PaddingValues
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material3.AssistChip
import androidx.compose.material3.AssistChipDefaults
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.getValue
import androidx.compose.runtime.setValue
import androidx.compose.ui.Modifier
import androidx.compose.ui.geometry.Offset
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.Path
import androidx.compose.ui.unit.dp
import com.example.mindcare.data.FakeData
import com.example.mindcare.model.Mood
import com.example.mindcare.ui.components.GradientHeader
import com.example.mindcare.ui.components.InsightCard

@Composable
fun InsightsScreen(
    innerPadding: PaddingValues
) {
    var selectedRange by remember { mutableStateOf("Week") }
    val ranges = listOf("Week", "Month", "Year")

    LazyColumn(
        modifier = Modifier
            .fillMaxSize()
            .padding(innerPadding)
    ) {
        item {
            GradientHeader(
                title = "Your mood insights",
                subtitle = "See gentle trends and patterns over time.",
                emoji = "📈"
            )
        }
        item {
            Spacer(modifier = Modifier.height(16.dp))
            Column(modifier = Modifier.padding(horizontal = 20.dp)) {
                Text(text = "Timeline", style = MaterialTheme.typography.titleLarge)
                Spacer(modifier = Modifier.height(8.dp))
                androidx.compose.foundation.layout.Row {
                    ranges.forEach { range ->
                        val selected = range == selectedRange
                        AssistChip(
                            onClick = { selectedRange = range },
                            label = { Text(range) },
                            colors = AssistChipDefaults.assistChipColors(
                                containerColor = if (selected)
                                    MaterialTheme.colorScheme.primary.copy(alpha = 0.12f)
                                else
                                    MaterialTheme.colorScheme.surface,
                                labelColor = if (selected)
                                    MaterialTheme.colorScheme.primary
                                else
                                    MaterialTheme.colorScheme.onSurface
                            ),
                            modifier = Modifier.padding(end = 8.dp)
                        )
                    }
                }
            }
        }
        item {
            Spacer(modifier = Modifier.height(12.dp))
            MoodTrendChart(
                data = FakeData.moodHistory,
                modifier = Modifier
                    .padding(horizontal = 20.dp)
                    .height(180.dp)
                    .fillMaxWidth()
            )
        }
        item {
            Spacer(modifier = Modifier.height(16.dp))
            Text(
                text = "Pattern highlights",
                style = MaterialTheme.typography.titleLarge,
                modifier = Modifier.padding(horizontal = 20.dp)
            )
        }
        items(FakeData.insights) { insight ->
            InsightCard(insight = insight)
        }
        item { Spacer(modifier = Modifier.height(24.dp)) }
    }
}

@Composable
private fun MoodTrendChart(
    data: List<Pair<Long, Mood>>,
    modifier: Modifier = Modifier
) {
    if (data.isEmpty()) return

    val moodToValue = mapOf(
        Mood.LOW to 0f,
        Mood.OKAY to 0.4f,
        Mood.GOOD to 0.7f,
        Mood.GREAT to 1f
    )

    Canvas(modifier = modifier) {
        val sorted = data.sortedBy { it.first }
        val stepX = size.width / (sorted.size - 1).coerceAtLeast(1)

        // Baseline
        drawLine(
            color = MaterialTheme.colorScheme.primary.copy(alpha = 0.1f),
            start = Offset(0f, size.height),
            end = Offset(size.width, size.height),
            strokeWidth = 2f
        )

        val path = Path()
        sorted.forEachIndexed { index, (_, mood) ->
            val x = stepX * index
            val value = moodToValue[mood] ?: 0.5f
            val y = size.height - value * size.height * 0.8f
            if (index == 0) {
                path.moveTo(x, y)
            } else {
                path.lineTo(x, y)
            }
            // Dot
            drawCircle(
                color = MaterialTheme.colorScheme.primary,
                radius = 6f,
                center = Offset(x, y)
            )
        }

        drawPath(
            path = path,
            color = MaterialTheme.colorScheme.primary,
            style = androidx.compose.ui.graphics.drawscope.Stroke(width = 4f)
        )
    }
}


